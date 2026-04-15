<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use App\Models\ClinicSetting;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use RuntimeException;

class StaffAppointmentCreationService
{
    public function __construct(
        protected AppointmentAvailabilityService $availabilityService
    ) {
    }

    public function createDirectAppointment(
        int $patientId,
        int $serviceId,
        int $dentistId,
        string $appointmentDate,
        string $startTime,
        ?string $remarks = null
    ): Appointment {
        $service = Service::query()->findOrFail($serviceId);

        [$ok, $message] = $this->availabilityService->validateRequest(
            serviceId: $serviceId,
            preferredDentistId: $dentistId,
            date: $appointmentDate,
            startTime: $startTime
        );

        if (!$ok) {
            throw new RuntimeException($message ?: 'Selected appointment slot is not available.');
        }

        $start = Carbon::parse($appointmentDate . ' ' . $startTime);
        $end = $start->copy()->addMinutes((int) $service->estimated_duration_minutes);

        return DB::transaction(function () use (
            $patientId,
            $service,
            $dentistId,
            $appointmentDate,
            $start,
            $end,
            $remarks
        ) {
            $userId = Auth::id();
            $clinicSetting = ClinicSetting::query()->first();

            $appointment = Appointment::create([
                'appointment_code' => 'APT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
                'request_id' => null,
                'dentist_id' => $dentistId,
                'patient_id' => $patientId,
                'service_id' => $service->service_id,
                'appointment_date' => $appointmentDate,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'estimated_duration_minutes' => (int) $service->estimated_duration_minutes,
                'estimated_price' => $service->estimated_price,
                'status' => 'confirmed',
                'arrival_status' => 'pending',
                'grace_period_minutes' => $clinicSetting?->default_no_show_minutes ?? 30,
                'booked_by' => $userId,
                'confirmed_by' => $userId,
                'remarks' => $remarks,
            ]);

            AppointmentStatusLog::create([
                'appointment_id' => $appointment->appointment_id,
                'old_status' => null,
                'new_status' => 'confirmed',
                'changed_by' => $userId,
                'remarks' => 'Directly created by staff.',
                'changed_at' => now(),
            ]);

            return $appointment;
        });
    }
}
