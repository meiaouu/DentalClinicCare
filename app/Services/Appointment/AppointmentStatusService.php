<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AppointmentStatusService
{
    public function markArrived(Appointment $appointment, ?string $remarks = null): Appointment
    {
        if (!in_array($appointment->status, ['confirmed', 'rescheduled'], true)) {
            throw new RuntimeException('Only confirmed or rescheduled appointments can be marked as arrived.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: $appointment->status,
            updates: [
                'arrival_status' => 'arrived',
                'remarks' => $remarks ?? $appointment->remarks,
            ],
            logRemarks: $remarks ?: 'Patient marked as arrived.'
        );
    }

    public function checkIn(Appointment $appointment, ?string $remarks = null): Appointment
    {
        if (!in_array($appointment->status, ['confirmed', 'rescheduled'], true)) {
            throw new RuntimeException('Only confirmed or rescheduled appointments can be checked in.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: 'checked_in',
            updates: [
                'arrival_status' => 'checked_in',
                'checked_in_at' => now(),
                'remarks' => $remarks ?? $appointment->remarks,
            ],
            logRemarks: $remarks ?: 'Patient checked in.'
        );
    }

    public function markInProgress(Appointment $appointment, ?string $remarks = null): Appointment
    {
        if ($appointment->status !== 'checked_in') {
            throw new RuntimeException('Only checked-in appointments can be marked in progress.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: 'in_progress',
            updates: [
                'remarks' => $remarks ?? $appointment->remarks,
            ],
            logRemarks: $remarks ?: 'Appointment marked in progress.'
        );
    }

    public function complete(Appointment $appointment, ?string $remarks = null): Appointment
    {
        if (!in_array($appointment->status, ['checked_in', 'in_progress'], true)) {
            throw new RuntimeException('Only checked-in or in-progress appointments can be completed.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: 'completed',
            updates: [
                'completed_at' => now(),
                'remarks' => $remarks ?? $appointment->remarks,
            ],
            logRemarks: $remarks ?: 'Appointment completed.'
        );
    }

    public function markNoShow(Appointment $appointment, ?string $remarks = null): Appointment
    {
        if (!in_array($appointment->status, ['confirmed', 'rescheduled'], true)) {
            throw new RuntimeException('Only confirmed or rescheduled appointments can be marked as no-show.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: 'no_show',
            updates: [
                'arrival_status' => 'no_show',
                'no_show_at' => now(),
                'remarks' => $remarks ?? $appointment->remarks,
            ],
            logRemarks: $remarks ?: 'Appointment marked as no-show.'
        );
    }

    public function cancel(Appointment $appointment, ?string $reason = null): Appointment
    {
        if (in_array($appointment->status, ['completed', 'cancelled', 'no_show'], true)) {
            throw new RuntimeException('This appointment can no longer be cancelled.');
        }

        return $this->updateAppointmentStatus(
            appointment: $appointment,
            newStatus: 'cancelled',
            updates: [
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => $reason,
                'remarks' => $reason ?? $appointment->remarks,
            ],
            logRemarks: $reason ?: 'Appointment cancelled.'
        );
    }

    protected function updateAppointmentStatus(
        Appointment $appointment,
        string $newStatus,
        array $updates,
        string $logRemarks
    ): Appointment {
        return DB::transaction(function () use ($appointment, $newStatus, $updates, $logRemarks) {
            $oldStatus = $appointment->status;

            $appointment->update(array_merge($updates, [
                'status' => $newStatus,
            ]));

            AppointmentStatusLog::create([
                'appointment_id' => $appointment->appointment_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => Auth::id(),
                'remarks' => $logRemarks,
                'changed_at' => now(),
            ]);

            return $appointment->fresh();
        });
    }
}
