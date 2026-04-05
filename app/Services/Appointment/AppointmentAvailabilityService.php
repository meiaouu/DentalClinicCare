<?php

namespace App\Services\Appointment;

use App\Models\ClinicSetting;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentAvailabilityService
{
    public function validateRequest(
        int $serviceId,
        ?int $preferredDentistId,
        string $date,
        string $startTime
    ): array {
        $service = Service::findOrFail($serviceId);
        $clinic = ClinicSetting::firstOrFail();

        $dateCarbon = Carbon::parse($date);
        $start = Carbon::parse($date . ' ' . $startTime);
        $end = (clone $start)->addMinutes((int) $service->estimated_duration_minutes);

        if ($start->isPast()) {
            return [false, 'Selected date and time cannot be in the past.'];
        }

        $clinicOpen = Carbon::parse($date . ' ' . $clinic->open_time);
        $clinicClose = Carbon::parse($date . ' ' . $clinic->close_time);

        if ($start->lt($clinicOpen) || $end->gt($clinicClose)) {
            return [false, 'Selected time is outside clinic operating hours.'];
        }

        if ($preferredDentistId) {
            if (DB::getSchemaBuilder()->hasTable('dentist_schedules')) {
                $dayOfWeek = strtolower($dateCarbon->format('l'));

                $hasSchedule = DB::table('dentist_schedules')
                    ->where('dentist_id', $preferredDentistId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true)
                    ->where('start_time', '<=', $start->format('H:i:s'))
                    ->where('end_time', '>=', $end->format('H:i:s'))
                    ->exists();

                if (!$hasSchedule) {
                    return [false, 'Selected dentist is not available at that time.'];
                }
            }

            if (DB::getSchemaBuilder()->hasTable('dentist_schedule_blocks')) {
                $hasBlock = DB::table('dentist_schedule_blocks')
                    ->where('dentist_id', $preferredDentistId)
                    ->where('block_date', $date)
                    ->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end->format('H:i:s'))
                            ->where('end_time', '>', $start->format('H:i:s'));
                    })
                    ->exists();

                if ($hasBlock) {
                    return [false, 'Selected dentist has a blocked schedule at that time.'];
                }
            }

            if (DB::getSchemaBuilder()->hasTable('dentist_unavailable_dates')) {
                $hasUnavailable = DB::table('dentist_unavailable_dates')
                    ->where('dentist_id', $preferredDentistId)
                    ->where('unavailable_date', $date)
                    ->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end->format('H:i:s'))
                            ->where('end_time', '>', $start->format('H:i:s'));
                    })
                    ->exists();

                if ($hasUnavailable) {
                    return [false, 'Selected dentist is unavailable at that time.'];
                }
            }

            if (DB::getSchemaBuilder()->hasTable('appointments')) {
                $hasConflict = DB::table('appointments')
                    ->where('dentist_id', $preferredDentistId)
                    ->where('appointment_date', $date)
                    ->whereIn('status', ['confirmed', 'checked_in', 'in_progress'])
                    ->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end->format('H:i:s'))
                            ->where('end_time', '>', $start->format('H:i:s'));
                    })
                    ->exists();

                if ($hasConflict) {
                    return [false, 'Selected time overlaps with an existing confirmed appointment.'];
                }
            }
        }

        return [true, null];
    }
}
