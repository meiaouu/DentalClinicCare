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
use App\Services\Booking\BookingAvailabilityService;
use InvalidArgumentException;

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

    if (!in_array($requestModel->request_status, ['pending', 'under_review'], true)) {
        throw new RuntimeException('Request cannot be confirmed.');
    }

    $service = Service::findOrFail($requestModel->service_id);

    $normalizedDate = $this->normalizeDate($appointmentDate);
    $normalizedStart = $this->normalizeTime($startTime);

    $isAvailable = $this->availabilityService->isRequestedSlotAvailable(
        $normalizedDate,
        $normalizedStart,
        (int) $requestModel->service_id,
        $dentistId
    );

    if (!$isAvailable) {
        throw new RuntimeException('Selected dentist is not available at that time.');
    }

    $start = Carbon::parse($normalizedDate . ' ' . $normalizedStart);
    $end = $start->copy()->addMinutes((int) $service->estimated_duration_minutes);

    return DB::transaction(function () use (
        $requestModel,
        $dentistId,
        $normalizedDate,
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
            'appointment_date' => $normalizedDate,
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
            'preferred_date' => $normalizedDate,
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

    $normalizedDate = $this->normalizeDate($appointmentDate);
    $normalizedStart = $this->normalizeTime($startTime);

    $isAvailable = $this->availabilityService->isRequestedSlotAvailable(
        $normalizedDate,
        $normalizedStart,
        (int) $requestModel->service_id,
        $dentistId
    );

    if (!$isAvailable) {
        throw new RuntimeException('New slot is not available at that time.');
    }

    $requestModel->update([
        'preferred_dentist_id' => $dentistId,
        'preferred_date' => $normalizedDate,
        'preferred_start_time' => $normalizedStart,
        'request_status' => 'under_review',
        'reviewed_by_user_id' => Auth::id(),
        'reviewed_at' => now(),
        'review_notes' => $staffNotes,
    ]);
}

    protected function normalizeDate(string $date): string
{
    $date = trim($date);

    try {
        return Carbon::parse($date)->format('Y-m-d');
    } catch (\Throwable) {
        throw new InvalidArgumentException('Invalid appointment date format.');
    }
}

protected function normalizeTime(string $time): string
{
    $time = trim($time);

    // Accept H:i or H:i:s
    foreach (['H:i', 'H:i:s'] as $format) {
        try {
            return Carbon::createFromFormat($format, $time)->format('H:i:s');
        } catch (\Throwable) {
            // try next format
        }
    }

    throw new InvalidArgumentException('Invalid appointment time format.');
}


}
