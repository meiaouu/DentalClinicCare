<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $conversations = Conversation::query()
            ->with([
                'patient',
                'appointmentRequest',
                'handler',
                'latestMessage',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->orWhere('guest_name', 'like', "%{$search}%")
                        ->orWhere('guest_contact_number', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('contact_number', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('last_message_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.chat.index', compact('conversations', 'search'));
    }

    public function show(Conversation $conversation): View
{
    $conversation->load([
        'patient',
        'appointmentRequest',
        'handler',
        'messages.senderUser',
    ]);

    Message::query()
        ->where('conversation_id', $conversation->conversation_id)
        ->whereNull('read_at')
        ->whereIn('sender_type', ['patient', 'guest'])
        ->update(['read_at' => now()]);

    $conversations = Conversation::query()
        ->with([
            'patient',
            'latestMessage',
        ])
        ->orderByDesc('last_message_at')
        ->get();

    return view('staff.chat.show', compact('conversation', 'conversations'));
}

public function close(Conversation $conversation): RedirectResponse
{
    $conversation->update([
        'conversation_status' => 'closed',
    ]);

    return back()->with('success', 'Conversation marked as closed.');
}

public function reopen(Conversation $conversation): RedirectResponse
{
    $conversation->update([
        'conversation_status' => 'open',
    ]);

    return back()->with('success', 'Conversation reopened.');
}

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'message_text' => ['required', 'string', 'max:2000'],
        ]);

        $staffUser = Auth::user();
        abort_unless($staffUser, 403);

        if (!$conversation->handled_by) {
            $conversation->update(['handled_by' => $staffUser->user_id]);
        }

        $messageText = trim($validated['message_text']);

Message::create([
    'conversation_id' => $conversation->conversation_id,
    'sender_user_id' => $staffUser->user_id,
    'sender_type' => 'staff',
    'message_text' => $messageText,
    'message_body' => $messageText,
    'is_bot_reply' => false,
    'sent_at' => now(),
]);

        $conversation->update([
            'handled_by' => $conversation->handled_by ?: $staffUser->user_id,
            'conversation_status' => 'open',
            'last_message_at' => now(),
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function fetch(Conversation $conversation): \Illuminate\Http\JsonResponse
{
    $messages = $conversation->messages()
        ->orderBy('message_id')
        ->get();

    Message::query()
        ->where('conversation_id', $conversation->conversation_id)
        ->whereNull('read_at')
        ->whereIn('sender_type', ['patient', 'guest'])
        ->update(['read_at' => now()]);

    return response()->json([
        'messages' => $messages,
    ]);
}

    public function assign(Conversation $conversation): RedirectResponse
    {
        $staffUser = Auth::user();
        abort_unless($staffUser, 403);

        $conversation->update([
            'handled_by' => $staffUser->user_id,
            'conversation_status' => 'open',
        ]);

        return back()->with('success', 'Conversation assigned to you.');
    }

}
