<?php

namespace App\Services\Appointment;

use App\Services\Schedule\AvailabilityService;
use App\Services\Schedule\ConflictCheckerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AppointmentService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected ConflictCheckerService $conflictCheckerService
    ) {}

    public function computeEndTime(int $serviceId, string $date, string $startTime): string
    {
        $service = DB::table('services')->where('service_id', $serviceId)->first();

        if (!$service) {
            throw new RuntimeException('Selected service does not exist.');
        }

        return Carbon::parse($date . ' ' . $startTime)
            ->addMinutes((int) $service->estimated_duration_minutes)
            ->format('H:i:s');
    }

    public function validateScheduleRules(string $date, string $startTime, string $endTime, ?int $dentistId): void
    {
        $clinicHours = $this->availabilityService->getClinicHoursForDate($date);

        if (!$clinicHours) {
            throw new RuntimeException('Clinic is closed on the selected date.');
        }

        if ($startTime < $clinicHours['open_time'] || $endTime > $clinicHours['close_time']) {
            throw new RuntimeException('Selected time is outside clinic hours.');
        }

        if ($this->availabilityService->isClinicBlocked($date, $startTime, $endTime)) {
            throw new RuntimeException('Clinic schedule is blocked for the selected time.');
        }

        if ($this->availabilityService->isDentistBlocked($dentistId, $date, $startTime, $endTime)) {
            throw new RuntimeException('Selected dentist is unavailable for the selected time.');
        }

        if ($this->conflictCheckerService->hasApprovedAppointmentConflict($dentistId, $date, $startTime, $endTime)) {
            throw new RuntimeException('Selected slot is no longer available.');
        }
    }

    public function createPendingRequest(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $startTime = strlen($data['preferred_start_time']) === 5
                ? $data['preferred_start_time'] . ':00'
                : $data['preferred_start_time'];

            $endTime = $this->computeEndTime(
                (int) $data['service_id'],
                $data['preferred_date'],
                $startTime
            );

            $this->validateScheduleRules(
                $data['preferred_date'],
                $startTime,
                $endTime,
                $data['preferred_dentist_id'] ?? null
            );

            $requestId = DB::table('appointment_requests')->insertGetId([
                'patient_id' => Auth::check() ? Auth::id() : null,
                'is_guest' => !Auth::check(),
                'service_id' => $data['service_id'],
                'preferred_dentist_id' => $data['preferred_dentist_id'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'sex' => $data['sex'],
                'birth_date' => $data['birth_date'],
                'civil_status' => $data['civil_status'],
                'occupation' => $data['occupation'] ?? null,
                'contact_number' => $data['contact_number'],
                'email' => $data['email'],
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
                'region' => $data['region'],
                'province' => $data['province'],
                'city' => $data['city'],
                'barangay' => $data['barangay'],
                'address_line' => $data['address_line'] ?? null,
                'preferred_date' => $data['preferred_date'],
                'preferred_start_time' => $startTime,
                'preferred_end_time' => $endTime,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (($data['answers'] ?? []) as $optionId => $answer) {
                DB::table('appointment_request_answers')->insert([
                    'appointment_request_id' => $requestId,
                    'option_id' => (int) $optionId,
                    'answer_value' => is_array($answer) ? json_encode($answer) : (string) $answer,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'create_pending_appointment_request',
                'entity_type' => 'appointment_request',
                'entity_id' => $requestId,
                'description' => 'Created pending appointment request.',
                'new_values' => json_encode([
                    'preferred_date' => $data['preferred_date'],
                    'preferred_start_time' => $startTime,
                    'preferred_end_time' => $endTime,
                    'service_id' => $data['service_id'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $requestId;
        });
    }

    public function approveRequest(int $appointmentRequestId, ?int $dentistId, string $date, string $startTime): int
    {
        return DB::transaction(function () use ($appointmentRequestId, $dentistId, $date, $startTime) {
            $request = DB::table('appointment_requests')
                ->where('appointment_request_id', $appointmentRequestId)
                ->lockForUpdate()
                ->first();

            if (!$request) {
                throw new RuntimeException('Appointment request not found.');
            }

            if ($request->status !== 'pending') {
                throw new RuntimeException('Only pending requests can be approved.');
            }

            $startTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
            $resolvedDentistId = $dentistId ?: $request->preferred_dentist_id;

            $endTime = $this->computeEndTime(
                (int) $request->service_id,
                $date,
                $startTime
            );

            $this->validateScheduleRules($date, $startTime, $endTime, $resolvedDentistId);

            $appointmentId = DB::table('appointments')->insertGetId([
                'appointment_request_id' => $request->appointment_request_id,
                'patient_id' => $request->patient_id,
                'service_id' => $request->service_id,
                'dentist_id' => $resolvedDentistId,
                'appointment_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'approved',
                'notes' => $request->notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('appointment_requests')
                ->where('appointment_request_id', $appointmentRequestId)
                ->update([
                    'status' => 'approved',
                    'preferred_dentist_id' => $resolvedDentistId,
                    'preferred_date' => $date,
                    'preferred_start_time' => $startTime,
                    'preferred_end_time' => $endTime,
                    'updated_at' => now(),
                ]);

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'approve_appointment_request',
                'entity_type' => 'appointment_request',
                'entity_id' => $appointmentRequestId,
                'description' => 'Approved appointment request.',
                'new_values' => json_encode([
                    'appointment_id' => $appointmentId,
                    'dentist_id' => $resolvedDentistId,
                    'appointment_date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $appointmentId;
        });
    }

    public function rejectRequest(int $appointmentRequestId, ?string $reason = null): void
    {
        DB::transaction(function () use ($appointmentRequestId, $reason) {
            DB::table('appointment_requests')
                ->where('appointment_request_id', $appointmentRequestId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'updated_at' => now(),
                ]);

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'reject_appointment_request',
                'entity_type' => 'appointment_request',
                'entity_id' => $appointmentRequestId,
                'description' => $reason ?: 'Rejected appointment request.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
