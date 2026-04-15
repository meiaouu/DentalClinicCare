<?php

namespace App\Notifications;

use App\Models\AppointmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAppointmentRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public AppointmentRequest $appointmentRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
{
    $name = trim(
        ($this->appointmentRequest->guest_first_name ?? '') . ' ' .
        ($this->appointmentRequest->guest_last_name ?? '')
    );

    if ($this->appointmentRequest->patient) {
        $name = trim(
            ($this->appointmentRequest->patient->first_name ?? '') . ' ' .
            ($this->appointmentRequest->patient->last_name ?? '')
        );
    }

    return [
        'type' => 'appointment_request',
        'request_id' => $this->appointmentRequest->request_id,

        // THIS FIXES REDIRECTION
        'redirect_url' => route(
            'staff.appointment-requests.show',
            $this->appointmentRequest->request_id
        ),

        'title' => 'New Appointment Request',
        'message' => 'New booking request from ' . ($name ?: 'Guest Patient'),
    ];
}
}
