<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Ai\ClinicChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class PublicMessageController extends Controller
{
    public function __construct(
        protected ClinicChatbotService $chatbotService
    ) {
    }

    public function patientForm(): View
    {
        $user = Auth::user();

        abort_unless($user && $user->patient, 403);

        $conversation = $this->findOrCreatePatientConversation((int) $user->patient->patient_id);

        $conversation->load([
            'messages' => function ($query) {
                $query->orderBy('message_id');
            },
        ]);

        return view('patient.chat.index', compact('conversation'));
    }

    public function patientSend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        abort_unless($user && $user->patient, 403);

        $conversation = $this->findOrCreatePatientConversation((int) $user->patient->patient_id);

        $messageText = trim($validated['message_text']);

        $userMessage = Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => $user->user_id,
            'sender_type' => 'patient',
            'message_text' => $messageText,
            'message_body' => $messageText,
            'is_bot_reply' => false,
            'sent_at' => now(),
        ]));

        $botPayload = $this->chatbotService->generateReplyPayload($messageText, false);
        $botMessage = null;

        if (($conversation->handled_by ?? null) === null || ($botPayload['needs_handoff'] ?? false)) {
            $botReply = (string) ($botPayload['reply'] ?? '');

            if ($botReply !== '') {
                $botMessage = Message::create($this->buildMessageAttributes([
                    'conversation_id' => $conversation->conversation_id,
                    'sender_user_id' => null,
                    'sender_type' => 'bot',
                    'message_text' => $botReply,
                    'message_body' => $botReply,
                    'is_bot_reply' => true,
                    'sent_at' => now(),
                ]));
            }
        }

        $this->safeUpdateConversation($conversation, [
            'conversation_status' => ($botPayload['needs_handoff'] ?? false) ? 'pending_staff' : 'open',
            'last_message_at' => now(),
        ]);

        $this->notifyStaffOfIncomingConversation($conversation, $messageText);

        return response()->json([
            'success' => true,
            'user_message' => $userMessage,
            'bot_message' => $botMessage,
            'conversation_status' => $conversation->conversation_status ?? 'open',
        ]);
    }

    public function patientFetch(): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user && $user->patient, 403);

        $conversationQuery = Conversation::query()->where('patient_id', $user->patient->patient_id);

        if ($this->conversationHasColumn('is_guest')) {
            $conversationQuery->where('is_guest', false);
        }

        $conversation = $conversationQuery->first();

        if (!$conversation) {
            return response()->json([
                'messages' => [],
            ]);
        }

        $messages = Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->orderBy('message_id')
            ->get();

        return response()->json([
            'messages' => $messages,
            'conversation_status' => $conversation->conversation_status ?? 'open',
        ]);
    }

    public function widgetStart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_name' => ['nullable', 'string', 'max:100'],
            'guest_contact_number' => ['nullable', 'string', 'max:30'],
        ]);

        $conversationId = session('guest_widget_conversation_id');

        if ($conversationId) {
            $conversation = Conversation::query()->find($conversationId);

            if ($conversation) {
                return response()->json([
                    'success' => true,
                    'conversation_id' => $conversation->conversation_id,
                ]);
            }
        }

        $attributes = [
            'conversation_status' => 'bot_only',
        ];

        if ($this->conversationHasColumn('patient_id')) {
            $attributes['patient_id'] = null;
        }

        if ($this->conversationHasColumn('handled_by')) {
            $attributes['handled_by'] = null;
        }

        if ($this->conversationHasColumn('is_guest')) {
            $attributes['is_guest'] = true;
        }

        if ($this->conversationHasColumn('guest_name')) {
            $attributes['guest_name'] = $validated['guest_name'] ?? 'Guest';
        }

        if ($this->conversationHasColumn('guest_contact_number')) {
            $attributes['guest_contact_number'] = $validated['guest_contact_number'] ?? null;
        }

        if ($this->conversationHasColumn('last_message_at')) {
            $attributes['last_message_at'] = now();
        }

        $conversation = Conversation::query()->create($this->filterConversationColumns($attributes));

        $welcomeText = 'Hello. Welcome to our dental clinic chat support. I can help with booking guidance, clinic hours, follow-up guidance, and general clinic questions.';

        Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => null,
            'sender_type' => 'bot',
            'message_text' => $welcomeText,
            'message_body' => $welcomeText,
            'is_bot_reply' => true,
            'sent_at' => now(),
        ]));

        session([
            'guest_widget_conversation_id' => $conversation->conversation_id,
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->conversation_id,
        ]);
    }

    public function widgetSend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:2000'],
        ]);

        $conversationId = session('guest_widget_conversation_id');

        if (!$conversationId) {
            return response()->json([
                'success' => false,
                'message' => 'Chat session not found.',
            ], 422);
        }

        $conversation = Conversation::query()->find($conversationId);

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.',
            ], 404);
        }

        $messageText = trim($validated['message_text']);

        $userMessage = Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => null,
            'sender_type' => 'guest',
            'message_text' => $messageText,
            'message_body' => $messageText,
            'is_bot_reply' => false,
            'sent_at' => now(),
        ]));

        $botPayload = $this->chatbotService->generateReplyPayload($messageText, true);
        $botReply = (string) ($botPayload['reply'] ?? '');

        $botMessage = null;

        if ($botReply !== '') {
            $botMessage = Message::create($this->buildMessageAttributes([
                'conversation_id' => $conversation->conversation_id,
                'sender_user_id' => null,
                'sender_type' => 'bot',
                'message_text' => $botReply,
                'message_body' => $botReply,
                'is_bot_reply' => true,
                'sent_at' => now(),
            ]));
        }

        $this->safeUpdateConversation($conversation, [
            'conversation_status' => ($botPayload['needs_handoff'] ?? false) ? 'pending_staff' : 'bot_only',
            'last_message_at' => now(),
        ]);

        $this->notifyStaffOfIncomingConversation($conversation, $messageText);

        return response()->json([
            'success' => true,
            'user_message' => $userMessage,
            'bot_message' => $botMessage,
            'conversation_status' => $conversation->conversation_status ?? 'bot_only',
        ]);
    }

    public function widgetFetch(): JsonResponse
    {
        $conversationId = session('guest_widget_conversation_id');

        if (!$conversationId) {
            return response()->json([
                'messages' => [],
            ]);
        }

        $conversation = Conversation::query()->find($conversationId);

        if (!$conversation) {
            return response()->json([
                'messages' => [],
            ]);
        }

        $messages = Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->orderBy('message_id')
            ->get();

        return response()->json([
            'messages' => $messages,
            'conversation_status' => $conversation->conversation_status ?? 'bot_only',
        ]);
    }

    protected function findOrCreatePatientConversation(int $patientId): Conversation
    {
        $lookup = [
            'patient_id' => $patientId,
        ];

        if ($this->conversationHasColumn('is_guest')) {
            $lookup['is_guest'] = false;
        }

        $create = [
            'conversation_status' => 'open',
        ];

        if ($this->conversationHasColumn('last_message_at')) {
            $create['last_message_at'] = now();
        }

        return Conversation::query()->firstOrCreate($lookup, $this->filterConversationColumns($create));
    }

    protected function notifyStaffOfIncomingConversation(Conversation $conversation, string $messageText): void
    {
        try {
            if (!class_exists(\App\Notifications\NewChatMessageNotification::class)) {
                return;
            }

            $staffUsers = User::query()
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'staff');
                })
                ->get();

            if ($staffUsers->isEmpty()) {
                return;
            }

            $notificationClass = \App\Notifications\NewChatMessageNotification::class;

            Notification::send($staffUsers, new $notificationClass($conversation, $messageText));
        } catch (Throwable $e) {
            // Silent fail to avoid chat crash
        }
    }

    protected function safeUpdateConversation(Conversation $conversation, array $attributes): void
    {
        $filtered = $this->filterConversationColumns($attributes);

        if (!empty($filtered)) {
            $conversation->update($filtered);
        }
    }

    protected function buildMessageAttributes(array $attributes): array
    {
        return $this->filterExistingColumns((new Message())->getTable(), $attributes);
    }

    protected function filterConversationColumns(array $attributes): array
    {
        return $this->filterExistingColumns((new Conversation())->getTable(), $attributes);
    }

    protected function filterExistingColumns(string $table, array $attributes): array
    {
        $filtered = [];

        foreach ($attributes as $key => $value) {
            if (Schema::hasColumn($table, $key)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    protected function conversationHasColumn(string $column): bool
    {
        return Schema::hasColumn((new Conversation())->getTable(), $column);
    }
}
