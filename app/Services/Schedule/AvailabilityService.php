<?php

namespace App\Services\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    public function getClinicHoursForDate(string $date): ?array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $rule = DB::table('clinic_schedule_rules')
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$rule || !$rule->is_open) {
            return null;
        }

        return [
            'open_time' => $rule->open_time,
            'close_time' => $rule->close_time,
        ];
    }

    public function isClinicBlocked(string $date, string $startTime, string $endTime): bool
    {
        return DB::table('schedule_blocks')
            ->where('scope', 'clinic')
            ->where('block_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('is_full_day', true)
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();
    }

    public function isDentistBlocked(?int $dentistId, string $date, string $startTime, string $endTime): bool
    {
        if (!$dentistId) {
            return false;
        }

        $manualBlock = DB::table('schedule_blocks')
            ->where('scope', 'dentist')
            ->where('dentist_id', $dentistId)
            ->where('block_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('is_full_day', true)
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();

        $unavailable = DB::table('dentist_unavailable_dates')
            ->where('dentist_id', $dentistId)
            ->where('unavailable_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereNull('start_time')
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();

        return $manualBlock || $unavailable;
    }
}
