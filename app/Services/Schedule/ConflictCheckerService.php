<?php

namespace App\Services\Schedule;

use Illuminate\Support\Facades\DB;

class ConflictCheckerService
{
    public function hasApprovedAppointmentConflict(?int $dentistId, string $date, string $startTime, string $endTime): bool
    {
        $query = DB::table('appointments')
            ->where('appointment_date', $date)
            ->whereIn('status', ['approved', 'completed'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }

        return $query->exists();
    }
}
