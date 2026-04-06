<?php

namespace App\Services\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SlotGeneratorService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected ConflictCheckerService $conflictCheckerService
    ) {}

    public function generate(string $date, int $serviceId, ?int $dentistId = null): array
    {
        $service = DB::table('services')->where('service_id', $serviceId)->first();

        if (!$service) {
            return ['clinic_hours' => [], 'available_slots' => []];
        }

        $duration = (int) $service->estimated_duration_minutes;
        $hours = $this->availabilityService->getClinicHoursForDate($date);

        if (!$hours) {
            return ['clinic_hours' => [], 'available_slots' => []];
        }

        $clinicHours = [];
        $availableSlots = [];

        $cursor = Carbon::parse($date . ' ' . $hours['open_time']);
        $close = Carbon::parse($date . ' ' . $hours['close_time']);

        while ($cursor < $close) {
            $start = $cursor->format('H:i:s');
            $end = $cursor->copy()->addMinutes($duration)->format('H:i:s');

            $clinicHours[] = $cursor->format('H:i');

            $fitsClinicHours = Carbon::parse($date . ' ' . $end) <= $close;
            $blockedClinic = $this->availabilityService->isClinicBlocked($date, $start, $end);
            $blockedDentist = $this->availabilityService->isDentistBlocked($dentistId, $date, $start, $end);
            $conflict = $this->conflictCheckerService->hasApprovedAppointmentConflict($dentistId, $date, $start, $end);

            if ($fitsClinicHours && !$blockedClinic && !$blockedDentist && !$conflict) {
                $availableSlots[] = [
                    'start_time' => $cursor->format('H:i'),
                    'end_time' => Carbon::parse($date . ' ' . $end)->format('H:i'),
                ];
            }

            $cursor->addMinutes(30);
        }

        return [
            'clinic_hours' => $clinicHours,
            'available_slots' => $availableSlots,
        ];
    }
}
