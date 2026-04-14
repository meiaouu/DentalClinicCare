<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AppointmentRequest;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                                ->orWhere('guest_contact_number', 'like', "%{$search}%");
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

    public function storePatientThread(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $thread = MessageThread::create([
            'patient_id' => $patient->patient_id,
            'thread_type' => 'patient',
            'subject' => $validated['subject'] ?: 'Patient Message',
            'last_message_by_user_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        Message::create([
            'thread_id' => $thread->thread_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_body' => $validated['message_body'],
        ]);

        return redirect()
            ->route('staff.messages.show', $thread->thread_id)
            ->with('success', 'Message thread created.');
    }

    public function storeGuestRequestThread(Request $request, AppointmentRequest $appointmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $thread = MessageThread::create([
            'appointment_request_id' => $appointmentRequest->request_id,
            'thread_type' => 'guest_request',
            'subject' => $validated['subject'] ?: 'Guest Request Message',
            'last_message_by_user_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        Message::create([
            'thread_id' => $thread->thread_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_body' => $validated['message_body'],
        ]);

        return redirect()
            ->route('staff.messages.show', $thread->thread_id)
            ->with('success', 'Guest request thread created.');
    }

    public function reply(Request $request, MessageThread $thread): RedirectResponse
    {
        $validated = $request->validate([
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            'thread_id' => $thread->thread_id,
            'sender_user_id' => Auth::id(),
            'sender_type' => 'staff',
            'message_body' => $validated['message_body'],
        ]);

        $thread->update([
            'last_message_by_user_id' => Auth::id(),
            'last_message_at' => now(),
        ]);

        return back()->with('success', 'Reply sent.');
    }
}
