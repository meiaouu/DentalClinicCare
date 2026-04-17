<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AppointmentRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $conversations = Conversation::query()
            ->with([
                'patient',
                'messages' => function ($query) {
                    $query->latest('message_id')->limit(1);
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    if ($this->conversationHasColumn('guest_name')) {
                        $subQuery->where('guest_name', 'like', "%{$search}%");
                    }

                    if ($this->conversationHasColumn('guest_contact_number')) {
                        $subQuery->orWhere('guest_contact_number', 'like', "%{$search}%");
                    }

                    $subQuery->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc($this->conversationHasColumn('last_message_at') ? 'last_message_at' : 'conversation_id')
            ->paginate(15)
            ->withQueryString();

        return view('staff.messages.index', [
            'conversations' => $conversations,
            'threads' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load([
            'patient',
            'messages' => function ($query) {
                $query->orderBy('message_id');
            },
            'messages.senderUser',
        ]);

        $this->markIncomingMessagesAsRead($conversation);

        $conversations = Conversation::query()
            ->with([
                'patient',
                'messages' => function ($query) {
                    $query->latest('message_id')->limit(1);
                },
            ])
            ->orderByDesc($this->conversationHasColumn('last_message_at') ? 'last_message_at' : 'conversation_id')
            ->paginate(15);

        return view('staff.messages.show', [
            'conversation' => $conversation,
            'conversations' => $conversations,
        ]);
    }

    public function fetch(Conversation $conversation): JsonResponse
    {
        $messages = Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->orderBy('message_id')
            ->get();

        $this->markIncomingMessagesAsRead($conversation);

        return response()->json([
            'messages' => $messages,
            'conversation_status' => $conversation->conversation_status ?? 'open',
        ]);
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:5000'],
        ]);

        if (($conversation->conversation_status ?? null) === 'closed') {
            return back()->withErrors([
                'conversation' => 'This conversation is already closed.',
            ]);
        }

        $messageText = trim($validated['message_text']);

        Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_text' => $messageText,
            'message_body' => $messageText,
            'is_bot_reply' => false,
            'read_at' => now(),
            'sent_at' => now(),
        ]));

        $this->safeUpdateConversation($conversation, [
            'handled_by' => Auth::id(),
            'conversation_status' => 'open',
            'last_message_at' => now(),
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function close(Conversation $conversation): RedirectResponse
    {
        $this->safeUpdateConversation($conversation, [
            'conversation_status' => 'closed',
        ]);

        return back()->with('success', 'Conversation closed.');
    }

    public function reopen(Conversation $conversation): RedirectResponse
    {
        $this->safeUpdateConversation($conversation, [
            'conversation_status' => 'open',
        ]);

        return back()->with('success', 'Conversation reopened.');
    }

    public function assign(Conversation $conversation): RedirectResponse
    {
        $this->safeUpdateConversation($conversation, [
            'handled_by' => Auth::id(),
            'conversation_status' => 'pending_staff',
        ]);

        return back()->with('success', 'Conversation assigned to you.');
    }

    public function storePatientThread(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:5000'],
        ]);

        $conversation = $this->findOrCreatePatientConversation($patient->patient_id);

        Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_text' => trim($validated['message_text']),
            'message_body' => trim($validated['message_text']),
            'is_bot_reply' => false,
            'read_at' => now(),
            'sent_at' => now(),
        ]));

        $this->safeUpdateConversation($conversation, [
            'handled_by' => Auth::id(),
            'conversation_status' => 'open',
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('staff.messages.show', $conversation->conversation_id)
            ->with('success', 'Conversation started.');
    }

    public function storeGuestRequestThread(Request $request, AppointmentRequest $appointmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:5000'],
        ]);

        $conversation = $this->findOrCreateGuestConversation($appointmentRequest);

        Message::create($this->buildMessageAttributes([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_text' => trim($validated['message_text']),
            'message_body' => trim($validated['message_text']),
            'is_bot_reply' => false,
            'read_at' => now(),
            'sent_at' => now(),
        ]));

        $this->safeUpdateConversation($conversation, [
            'handled_by' => Auth::id(),
            'conversation_status' => 'open',
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('staff.messages.show', $conversation->conversation_id)
            ->with('success', 'Guest conversation started.');
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
            'conversation_status' => 'pending_staff',
        ];

        if ($this->conversationHasColumn('handled_by')) {
            $create['handled_by'] = Auth::id();
        }

        if ($this->conversationHasColumn('last_message_at')) {
            $create['last_message_at'] = now();
        }

        return Conversation::query()->firstOrCreate($lookup, $create);
    }

    protected function findOrCreateGuestConversation(AppointmentRequest $appointmentRequest): Conversation
    {
        $lookup = [];

        if ($this->conversationHasColumn('guest_contact_number') && !empty($appointmentRequest->guest_contact_number)) {
            $lookup['guest_contact_number'] = $appointmentRequest->guest_contact_number;
        } elseif ($this->conversationHasColumn('is_guest')) {
            $lookup['is_guest'] = true;
        }

        $create = [
            'conversation_status' => 'pending_staff',
        ];

        if ($this->conversationHasColumn('is_guest')) {
            $create['is_guest'] = true;
        }

        if ($this->conversationHasColumn('patient_id')) {
            $create['patient_id'] = null;
        }

        if ($this->conversationHasColumn('guest_name')) {
            $create['guest_name'] = trim((string) (($appointmentRequest->guest_first_name ?? '') . ' ' . ($appointmentRequest->guest_last_name ?? '')));
        }

        if ($this->conversationHasColumn('guest_contact_number')) {
            $create['guest_contact_number'] = $appointmentRequest->guest_contact_number;
        }

        if ($this->conversationHasColumn('handled_by')) {
            $create['handled_by'] = Auth::id();
        }

        if ($this->conversationHasColumn('last_message_at')) {
            $create['last_message_at'] = now();
        }

        if (empty($lookup)) {
            return Conversation::query()->create($this->filterConversationColumns($create));
        }

        return Conversation::query()->firstOrCreate($lookup, $this->filterConversationColumns($create));
    }

    protected function markIncomingMessagesAsRead(Conversation $conversation): void
    {
        if (!$this->messageHasColumn('read_at')) {
            return;
        }

        Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->whereNull('read_at')
            ->whereIn('sender_type', ['patient', 'guest', 'bot'])
            ->update(['read_at' => now()]);
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

    protected function messageHasColumn(string $column): bool
    {
        return Schema::hasColumn((new Message())->getTable(), $column);
    }
}
