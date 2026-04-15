<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Services\Messaging\MessageThreadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $threads = MessageThread::query()
            ->with([
                'patient',
                'appointmentRequest',
                'messages' => fn ($query) => $query->latest('message_id')->limit(1),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subject', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('contact_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('appointmentRequest', function ($requestQuery) use ($search) {
                            $requestQuery->where('guest_first_name', 'like', "%{$search}%")
                                ->orWhere('guest_last_name', 'like', "%{$search}%")
                                ->orWhere('guest_contact_number', 'like', "%{$search}%")
                                ->orWhere('request_code', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('last_message_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.messages.index', compact('threads'));
    }

    public function show(MessageThread $thread): View
    {
        $thread->load([
            'patient',
            'appointmentRequest',
            'messages.senderUser',
        ]);

        Message::query()
            ->where('thread_id', $thread->thread_id)
            ->whereNull('read_at')
            ->where('sender_type', '!=', 'staff')
            ->update(['read_at' => now()]);

        return view('staff.messages.show', compact('thread'));
    }

    public function reply(
        Request $request,
        MessageThread $thread,
        MessageThreadService $messageService
    ): RedirectResponse {
        $validated = $request->validate([
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $messageService->replyAsStaff(
            thread: $thread,
            messageBody: $validated['message_body']
        );

        return back()->with('success', 'Reply sent.');
    }
}
