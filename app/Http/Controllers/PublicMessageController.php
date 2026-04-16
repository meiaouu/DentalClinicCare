<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Chat\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PublicMessageController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {
    }

    public function patientForm(): View
    {
        $user = Auth::user();

        abort_unless($user && $user->patient, 403);

        $conversation = Conversation::query()->firstOrCreate(
            [
                'patient_id' => $user->patient->patient_id,
                'is_guest' => false,
            ],
            [
                'conversation_status' => 'open',
                'last_message_at' => now(),
            ]
        );

        $conversation->load('messages');

        Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->whereNull('read_at')
            ->whereIn('sender_type', ['staff', 'bot'])
            ->update([
                'read_at' => now(),
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

        $conversation = Conversation::query()->firstOrCreate(
            [
                'patient_id' => $user->patient->patient_id,
                'is_guest' => false,
            ],
            [
                'conversation_status' => 'open',
                'last_message_at' => now(),
            ]
        );

        $messageText = trim($validated['message_text']);

        $patientMessage = Message::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => $user->user_id,
            'sender_type' => 'patient',
            'message_text' => $messageText,
            'message_body' => $messageText,
            'is_bot_reply' => false,
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'conversation_status' => 'pending_staff',
        ]);

        if ($this->chatbotService->shouldBotReply($conversation)) {
            $botReply = $this->chatbotService->replyToConversation($conversation, $patientMessage->message_text);

            if ($botReply !== null && $botReply !== '') {
                Message::create([
                    'conversation_id' => $conversation->conversation_id,
                    'sender_user_id' => null,
                    'sender_type' => 'bot',
                    'message_text' => $botReply,
                    'message_body' => $botReply,
                    'is_bot_reply' => true,
                    'sent_at' => now(),
                ]);

                $conversation->update([
                    'last_message_at' => now(),
                    'conversation_status' => 'bot_only',
                ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function patientFetch(): JsonResponse
    {
        $user = Auth::user();

        abort_unless($user && $user->patient, 403);

        $conversation = Conversation::query()
            ->where('patient_id', $user->patient->patient_id)
            ->where('is_guest', false)
            ->first();

        if (!$conversation) {
            return response()->json([
                'messages' => [],
            ]);
        }

        $messages = Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->orderBy('message_id')
            ->get();

        Message::query()
            ->where('conversation_id', $conversation->conversation_id)
            ->whereNull('read_at')
            ->whereIn('sender_type', ['staff', 'bot'])
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function guestForm(string $requestCode): View
    {
        $requestModel = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        $conversation = Conversation::query()->firstOrCreate(
            [
                'appointment_request_id' => $requestModel->request_id,
                'is_guest' => true,
            ],
            [
                'guest_name' => trim(($requestModel->guest_first_name ?? '') . ' ' . ($requestModel->guest_last_name ?? '')),
                'guest_contact_number' => $requestModel->guest_contact_number,
                'conversation_status' => 'bot_only',
                'last_message_at' => now(),
            ]
        );

        $conversation->load('messages');

        return view('public.chat.guest', [
            'conversation' => $conversation,
            'requestModel' => $requestModel,
        ]);
    }

    public function guestSend(Request $request, string $requestCode): JsonResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:1000'],
        ]);

        $requestModel = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        $conversation = Conversation::query()->firstOrCreate(
            [
                'appointment_request_id' => $requestModel->request_id,
                'is_guest' => true,
            ],
            [
                'guest_name' => trim(($requestModel->guest_first_name ?? '') . ' ' . ($requestModel->guest_last_name ?? '')),
                'guest_contact_number' => $requestModel->guest_contact_number,
                'conversation_status' => 'bot_only',
                'last_message_at' => now(),
            ]
        );

        $messageText = trim($validated['message_text']);

        $guestMessage = Message::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => null,
            'sender_type' => 'guest',
            'message_text' => $messageText,
            'message_body' => $messageText,
            'is_bot_reply' => false,
            'sent_at' => now(),
        ]);

        $botReply = $this->chatbotService->replyToConversation($conversation, $guestMessage->message_text);

        if ($botReply !== null && $botReply !== '') {
            Message::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_user_id' => null,
                'sender_type' => 'bot',
                'message_text' => $botReply,
                'message_body' => $botReply,
                'is_bot_reply' => true,
                'sent_at' => now(),
            ]);
        }

        $conversation->update([
            'last_message_at' => now(),
            'conversation_status' => 'bot_only',
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function guestFetch(string $requestCode): JsonResponse
    {
        $requestModel = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        $conversation = Conversation::query()
            ->where('appointment_request_id', $requestModel->request_id)
            ->where('is_guest', true)
            ->first();

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
        ]);
    }

    public function guestBotForm(): View
{
    return view('public.chat.guest-bot');
}

public function guestBotStart(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'guest_name' => ['required', 'string', 'max:150'],
        'guest_contact_number' => ['required', 'string', 'max:30'],
    ]);

    $conversation = Conversation::query()->create([
        'patient_id' => null,
        'appointment_request_id' => null,
        'handled_by' => null,
        'conversation_status' => 'bot_only',
        'is_guest' => true,
        'guest_name' => trim($validated['guest_name']),
        'guest_contact_number' => trim($validated['guest_contact_number']),
        'last_message_at' => now(),
    ]);

    session([
        'guest_chat_conversation_id' => $conversation->conversation_id,
    ]);

    Message::create([
        'conversation_id' => $conversation->conversation_id,
        'sender_user_id' => null,
        'sender_type' => 'bot',
        'message_text' => 'Hello. I am the clinic support bot. I can help with clinic hours, booking guidance, follow-up scheduling guidance, and general clinic information.',
        'message_body' => 'Hello. I am the clinic support bot. I can help with clinic hours, booking guidance, follow-up scheduling guidance, and general clinic information.',
        'is_bot_reply' => true,
        'sent_at' => now(),
    ]);

    return redirect()->route('chat.guest.form');
}

public function guestBotSend(Request $request): JsonResponse
{

$conversationId = session('guest_chat_conversation_id');

if (!$conversationId) {
    $conversation = Conversation::create([
        'is_guest' => true,
        'conversation_status' => 'bot_only',
        'last_message_at' => now(),
    ]);

    session(['guest_chat_conversation_id' => $conversation->conversation_id]);
} else {
    $conversation = Conversation::findOrFail($conversationId);
}
    $validated = $request->validate([
        'message_text' => ['required', 'string', 'max:1000'],
    ]);

    $conversationId = session('guest_chat_conversation_id');
    abort_unless($conversationId, 403);

    $conversation = Conversation::query()
        ->where('conversation_id', $conversationId)
        ->where('is_guest', true)
        ->firstOrFail();

    $messageText = trim($validated['message_text']);

    Message::create([
        'conversation_id' => $conversation->conversation_id,
        'sender_user_id' => null,
        'sender_type' => 'guest',
        'message_text' => $messageText,
        'message_body' => $messageText,
        'is_bot_reply' => false,
        'sent_at' => now(),
    ]);

    $botReply = $this->chatbotService->replyToConversation($conversation, $messageText);

    if ($botReply !== null && $botReply !== '') {
        Message::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_user_id' => null,
            'sender_type' => 'bot',
            'message_text' => $botReply,
            'message_body' => $botReply,
            'is_bot_reply' => true,
            'sent_at' => now(),
        ]);
    }

    $conversation->update([
        'last_message_at' => now(),
        'conversation_status' => 'bot_only',
    ]);

    return response()->json([
        'success' => true,
    ]);
}

public function guestBotFetch(Conversation $conversation): JsonResponse
{
    $sessionConversationId = session('guest_chat_conversation_id');

    abort_unless(
        $sessionConversationId
        && (int) $sessionConversationId === (int) $conversation->conversation_id
        && $conversation->is_guest,
        403
    );

    $messages = Message::query()
        ->where('conversation_id', $conversation->conversation_id)
        ->orderBy('message_id')
        ->get();

    return response()->json([
        'conversation_id' => $conversation->conversation_id,
        'messages' => $messages,
    ]);
}
}
