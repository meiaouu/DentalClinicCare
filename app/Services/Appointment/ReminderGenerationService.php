<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\Reminder;
use Carbon\Carbon;

class ReminderGenerationService
{
    public function generateForAppointment(Appointment $appointment): void
    {
        $appointmentDateTime = Carbon::parse(
            $appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->start_time
        );

        $scheduleMap = [
            '3_days_before' => $appointmentDateTime->copy()->subDays(3),
            '2_days_before' => $appointmentDateTime->copy()->subDays(2),
            '1_day_before'  => $appointmentDateTime->copy()->subDay(),
        ];

        foreach ($scheduleMap as $type => $scheduledAt) {
            if ($scheduledAt->isPast()) {
                continue;
            }

            Reminder::create([
                'appointment_id' => $appointment->appointment_id,
                'patient_id' => $appointment->patient_id,
                'channel' => 'email',
                'reminder_type' => $type,
                'scheduled_at' => $scheduledAt,
                'status' => 'queued',
            ]);
        }
    }
}
