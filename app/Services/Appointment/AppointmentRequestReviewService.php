<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\AppointmentStatusLog;
use App\Models\ClinicSetting;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AppointmentRequestReviewService
{
    public function __construct(
        protected AppointmentAvailabilityService $availabilityService,
        protected ReminderGenerationService $reminderGenerationService
    ) {
    }

    public function confirm(
        AppointmentRequest $requestModel,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        ?string $staffNotes = null
    ): Appointment {
        if ($requestModel->request_status === 'converted_to_appointment') {
            throw new RuntimeException('Request already converted.');
        }

        if (!in_array($requestModel->request_status, ['pending', 'under_review'], true)) {
            throw new RuntimeException('Request cannot be confirmed.');
        }

        $service = Service::findOrFail($requestModel->service_id);

        [$ok, $message] = $this->availabilityService->validateRequest(
            serviceId: (int) $requestModel->service_id,
            preferredDentistId: $dentistId,
            date: $appointmentDate,
            startTime: $startTime
        );

        if (!$ok) {
            throw new RuntimeException($message ?: 'Time slot not available.');
        }

        $start = Carbon::parse($appointmentDate . ' ' . $startTime);
        $end = $start->copy()->addMinutes((int) $service->estimated_duration_minutes);

        return DB::transaction(function () use (
            $requestModel,
            $dentistId,
            $appointmentDate,
            $start,
            $end,
            $service,
            $staffNotes
        ) {
            $userId = Auth::id();
            $clinicSetting = ClinicSetting::query()->first();

            $appointment = Appointment::create([
                'appointment_code' => 'APT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
                'request_id' => $requestModel->request_id,
                'dentist_id' => $dentistId,
                'patient_id' => $requestModel->patient_id,
                'service_id' => $requestModel->service_id,
                'appointment_date' => $appointmentDate,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'estimated_duration_minutes' => (int) $service->estimated_duration_minutes,
                'estimated_price' => $service->estimated_price,
                'status' => 'confirmed',
                'grace_period_minutes' => $clinicSetting?->default_no_show_minutes ?? 30,
                'booked_by' => $userId,
                'confirmed_by' => $userId,
                'remarks' => $staffNotes,
            ]);

            $requestModel->update([
                'preferred_dentist_id' => $dentistId,
                'preferred_date' => $appointmentDate,
                'preferred_start_time' => $start->format('H:i:s'),
                'request_status' => 'converted_to_appointment',
                'reviewed_by_user_id' => $userId,
                'reviewed_at' => now(),
                'review_notes' => $staffNotes,
            ]);

            AppointmentStatusLog::create([
                'appointment_id' => $appointment->appointment_id,
                'old_status' => null,
                'new_status' => 'confirmed',
                'changed_by' => $userId,
                'remarks' => 'Converted from request #' . $requestModel->request_id,
                'changed_at' => now(),
            ]);

            $this->reminderGenerationService->generateForAppointment($appointment);

            return $appointment;
        });
    }

    public function reject(
        AppointmentRequest $requestModel,
        ?string $staffNotes = null
    ): void {
        if (!in_array($requestModel->request_status, ['pending', 'under_review'], true)) {
            throw new RuntimeException('Request cannot be rejected.');
        }

        $requestModel->update([
            'request_status' => 'rejected',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $staffNotes,
        ]);
    }

    public function reschedule(
        AppointmentRequest $requestModel,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        ?string $staffNotes = null
    ): void {
        if (!in_array($requestModel->request_status, ['pending', 'under_review'], true)) {
            throw new RuntimeException('Request cannot be rescheduled.');
        }

        [$ok, $message] = $this->availabilityService->validateRequest(
            serviceId: (int) $requestModel->service_id,
            preferredDentistId: $dentistId,
            date: $appointmentDate,
            startTime: $startTime
        );

        if (!$ok) {
            throw new RuntimeException($message ?: 'New slot not available.');
        }

        $normalizedStart = Carbon::parse($appointmentDate . ' ' . $startTime)->format('H:i:s');

        $requestModel->update([
            'preferred_dentist_id' => $dentistId,
            'preferred_date' => $appointmentDate,
            'preferred_start_time' => $normalizedStart,
            'request_status' => 'under_review',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $staffNotes,
        ]);
    }
}
