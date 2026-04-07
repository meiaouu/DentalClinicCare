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
use RuntimeException;

class AppointmentRequestReviewService
{
    public function __construct(
        protected AppointmentAvailabilityService $availabilityService
    ) {
    }

    public function confirm(
        AppointmentRequest $requestModel,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        ?string $staffNotes = null
    ): Appointment {
        if (!in_array($requestModel->request_status, ['pending', 'under_review', 'rescheduled'], true)) {
            throw new RuntimeException('Only pending, under review, or rescheduled requests can be confirmed.');
        }

        return DB::transaction(function () use ($requestModel, $dentistId, $appointmentDate, $startTime, $staffNotes) {
            $requestModel->refresh();

            [$isAvailable, $errorMessage] = $this->availabilityService->validateRequest(
                serviceId: (int) $requestModel->service_id,
                preferredDentistId: $dentistId,
                date: $appointmentDate,
                startTime: $startTime
            );

            if (!$isAvailable) {
                throw new RuntimeException($errorMessage ?: 'Selected slot is not available.');
            }

            $service = Service::findOrFail($requestModel->service_id);
            $clinic = ClinicSetting::first();

            $start = Carbon::parse($appointmentDate . ' ' . $startTime);
            $end = (clone $start)->addMinutes((int) $service->estimated_duration_minutes);

            $oldStatus = $requestModel->request_status;

            $appointment = Appointment::create([
                'request_id' => $requestModel->request_id,
                'patient_id' => $requestModel->patient_id,
                'dentist_id' => $dentistId,
                'service_id' => $requestModel->service_id,
                'appointment_date' => $appointmentDate,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'estimated_duration_minutes' => (int) $service->estimated_duration_minutes,
                'estimated_price' => (float) $service->estimated_price,
                'status' => 'confirmed',
                'grace_period_minutes' => $clinic?->default_no_show_minutes ?? 30,
                'notes' => $requestModel->notes,
            ]);

            $requestModel->update([
                'preferred_dentist_id' => $dentistId,
                'preferred_date' => $appointmentDate,
                'preferred_start_time' => $start->format('H:i:s'),
                'request_status' => 'converted_to_appointment',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
                'converted_appointment_id' => $appointment->appointment_id,
                'staff_notes' => $staffNotes,
            ]);

            AppointmentStatusLog::create([
                'request_id' => $requestModel->request_id,
                'changed_by_user_id' => Auth::id(),
                'entity_type' => 'request',
                'from_status' => $oldStatus,
                'to_status' => 'converted_to_appointment',
                'remarks' => $staffNotes,
            ]);

            AppointmentStatusLog::create([
                'request_id' => $requestModel->request_id,
                'appointment_id' => $appointment->appointment_id,
                'changed_by_user_id' => Auth::id(),
                'entity_type' => 'appointment',
                'from_status' => null,
                'to_status' => 'confirmed',
                'remarks' => 'Appointment created from request confirmation.',
            ]);

            return $appointment;
        });
    }

    public function reject(AppointmentRequest $requestModel, string $staffNotes): void
    {
        if (!in_array($requestModel->request_status, ['pending', 'under_review', 'rescheduled'], true)) {
            throw new RuntimeException('Only reviewable requests can be rejected.');
        }

        DB::transaction(function () use ($requestModel, $staffNotes) {
            $oldStatus = $requestModel->request_status;

            $requestModel->update([
                'request_status' => 'rejected',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
                'staff_notes' => $staffNotes,
            ]);

            AppointmentStatusLog::create([
                'request_id' => $requestModel->request_id,
                'changed_by_user_id' => Auth::id(),
                'entity_type' => 'request',
                'from_status' => $oldStatus,
                'to_status' => 'rejected',
                'remarks' => $staffNotes,
            ]);
        });
    }

    public function reschedule(
        AppointmentRequest $requestModel,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        string $staffNotes
    ): void {
        if (!in_array($requestModel->request_status, ['pending', 'under_review'], true)) {
            throw new RuntimeException('Only pending or under review requests can be rescheduled.');
        }

        [$isAvailable, $errorMessage] = $this->availabilityService->validateRequest(
            serviceId: (int) $requestModel->service_id,
            preferredDentistId: $dentistId,
            date: $appointmentDate,
            startTime: $startTime
        );

        if (!$isAvailable) {
            throw new RuntimeException($errorMessage ?: 'Selected new slot is not available.');
        }

        DB::transaction(function () use ($requestModel, $dentistId, $appointmentDate, $startTime, $staffNotes) {
            $oldStatus = $requestModel->request_status;

            $requestModel->update([
                'preferred_dentist_id' => $dentistId,
                'preferred_date' => $appointmentDate,
                'preferred_start_time' => Carbon::parse($appointmentDate . ' ' . $startTime)->format('H:i:s'),
                'request_status' => 'rescheduled',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
                'staff_notes' => $staffNotes,
            ]);

            AppointmentStatusLog::create([
                'request_id' => $requestModel->request_id,
                'changed_by_user_id' => Auth::id(),
                'entity_type' => 'request',
                'from_status' => $oldStatus,
                'to_status' => 'rescheduled',
                'remarks' => $staffNotes,
            ]);
        });
    }
}
