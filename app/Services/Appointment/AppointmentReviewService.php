<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\AppointmentStatusLog;
use App\Models\ClinicSetting;
use App\Models\Service;
use App\Services\Booking\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AppointmentRequestReviewService
{
    public function __construct(
        protected BookingAvailabilityService $availabilityService,
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

        if (!in_array($requestModel->request_status, ['pending', 'under_review', 'rescheduled'], true)) {
            throw new RuntimeException('Request cannot be confirmed.');
        }

        $service = Service::findOrFail($requestModel->service_id);
        $normalizedStart = $this->normalizeTime($startTime);

        $isAvailable = $this->availabilityService->isRequestedSlotAvailable(
            $appointmentDate,
            $normalizedStart,
            (int) $requestModel->service_id,
            $dentistId
        );

        if (!$isAvailable) {
            throw new RuntimeException('Selected schedule is no longer available.');
        }

        $start = Carbon::parse($appointmentDate . ' ' . $normalizedStart);
        $end = $start->copy()->addMinutes((int) $service->estimated_duration_minutes);

        return DB::transaction(function () use (
            $requestModel,
            $dentistId,
            $appointmentDate,
            $normalizedStart,
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
                'booked_by' => $userId,
                'confirmed_by' => $userId,
                'remarks' => $staffNotes,
                'grace_period_minutes' => $clinicSetting?->default_no_show_minutes ?? 30,
            ]);

            $requestModel->update([
                'preferred_dentist_id' => $dentistId,
                'preferred_date' => $appointmentDate,
                'preferred_start_time' => $normalizedStart,
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

    public function reschedule(
        AppointmentRequest $requestModel,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        ?string $staffNotes = null
    ): void {
        if (!in_array($requestModel->request_status, ['pending', 'under_review', 'rescheduled'], true)) {
            throw new RuntimeException('Request cannot be rescheduled.');
        }

        $normalizedStart = $this->normalizeTime($startTime);

        $isAvailable = $this->availabilityService->isRequestedSlotAvailable(
            $appointmentDate,
            $normalizedStart,
            (int) $requestModel->service_id,
            $dentistId
        );

        if (!$isAvailable) {
            throw new RuntimeException('New selected schedule is not available.');
        }

        $previous = [
            'preferred_dentist_id' => $requestModel->preferred_dentist_id,
            'preferred_date' => $requestModel->preferred_date,
            'preferred_start_time' => $requestModel->preferred_start_time,
        ];

        $requestModel->update([
            'preferred_dentist_id' => $dentistId,
            'preferred_date' => $appointmentDate,
            'preferred_start_time' => $normalizedStart,
            'request_status' => 'rescheduled',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => json_encode([
                'remarks' => $staffNotes,
                'previous_schedule' => $previous,
                'new_schedule' => [
                    'dentist_id' => $dentistId,
                    'appointment_date' => $appointmentDate,
                    'start_time' => $normalizedStart,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function reject(
        AppointmentRequest $requestModel,
        ?string $staffNotes = null
    ): void {
        if (!in_array($requestModel->request_status, ['pending', 'under_review', 'rescheduled'], true)) {
            throw new RuntimeException('Request cannot be rejected.');
        }

        $requestModel->update([
            'request_status' => 'rejected',
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $staffNotes,
        ]);
    }

    protected function normalizeTime(string $time): string
    {
        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (\Throwable) {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i:s');
        }
    }
}
