<?php

namespace App\Services\Messaging;

use App\Models\AppointmentRequest;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\ClinicNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageThreadService
{
    public function sendPatientMessage(
        Patient $patient,
        string $messageBody,
        ?string $subject = null
    ): MessageThread {
        return DB::transaction(function () use ($patient, $messageBody, $subject) {
            $thread = MessageThread::query()->firstOrCreate(
                [
                    'patient_id' => $patient->patient_id,
                    'thread_type' => 'patient',
                ],
                [
                    'subject' => $subject ?: 'Patient Message',
                ]
            );

            Message::create([
                'thread_id' => $thread->thread_id,
                'sender_user_id' => optional($patient->user)->user_id,
                'sender_type' => 'patient',
                'message_body' => $messageBody,
            ]);

            $thread->update([
                'subject' => $thread->subject ?: ($subject ?: 'Patient Message'),
                'last_message_by_user_id' => optional($patient->user)->user_id,
                'last_message_at' => now(),
            ]);

            $this->notifyClinic(
                title: 'New patient message',
                message: 'A patient sent a new message.',
                url: route('staff.messages.show', $thread->thread_id)
            );

            return $thread;
        });
    }

    public function sendGuestRequestMessage(
        AppointmentRequest $appointmentRequest,
        string $messageBody,
        ?string $subject = null
    ): MessageThread {
        return DB::transaction(function () use ($appointmentRequest, $messageBody, $subject) {
            $thread = MessageThread::query()->firstOrCreate(
                [
                    'appointment_request_id' => $appointmentRequest->request_id,
                    'thread_type' => 'guest_request',
                ],
                [
                    'subject' => $subject ?: 'Guest Request Message',
                ]
            );

            $guestName = trim(
                ($appointmentRequest->guest_first_name ?? '') . ' ' .
                ($appointmentRequest->guest_last_name ?? '')
            );

            Message::create([
                'thread_id' => $thread->thread_id,
                'sender_user_id' => null,
                'sender_type' => 'guest',
                'guest_name' => $guestName ?: 'Guest',
                'message_body' => $messageBody,
            ]);

            $thread->update([
                'subject' => $thread->subject ?: ($subject ?: 'Guest Request Message'),
                'last_message_by_user_id' => null,
                'last_message_at' => now(),
            ]);

            $this->notifyClinic(
                title: 'New guest message',
                message: 'A guest request sent a new message.',
                url: route('staff.messages.show', $thread->thread_id)
            );

            return $thread;
        });
    }

    public function replyAsStaff(MessageThread $thread, string $messageBody): void
    {
        DB::transaction(function () use ($thread, $messageBody) {
            Message::create([
                'thread_id' => $thread->thread_id,
                'sender_user_id' => Auth::id(),
                'sender_type' => 'staff',
                'message_body' => $messageBody,
            ]);

            $thread->update([
                'last_message_by_user_id' => Auth::id(),
                'last_message_at' => now(),
            ]);
        });
    }

    protected function notifyClinic(string $title, string $message, string $url): void
    {
        $staffUsers = User::query()
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'staff');
            })
            ->get();

        foreach ($staffUsers as $staffUser) {
            $staffUser->notify(new ClinicNotification([
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'type' => 'message',
            ]));
        }
    }
}
