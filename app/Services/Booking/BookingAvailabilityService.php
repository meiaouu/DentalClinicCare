<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\ClinicScheduleRule;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\DentistDateOverride;
use App\Models\DentistSchedule;
use App\Models\DentistUnavailableDate;
use App\Models\ScheduleBlock;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingAvailabilityService
{
    public function getAvailableDentists(string $date, int $serviceId): array
    {
        $service = Service::query()->findOrFail($serviceId);
        $duration = (int) $service->estimated_duration_minutes;

        if ($duration <= 0) {
            return [];
        }

        return Dentist::query()
            ->with('user')
            ->where('is_active', 1)
            ->get()
            ->filter(function (Dentist $dentist) use ($date, $duration) {
                return $this->hasAnyPossibleSlot($date, (int) $dentist->dentist_id, $duration);
            })
            ->map(function (Dentist $dentist) {
                $fullName = trim(collect([
                    $dentist->user?->first_name,
                    $dentist->user?->last_name,
                ])->filter()->implode(' '));

                return [
                    'dentist_id' => (int) $dentist->dentist_id,
                    'label' => $fullName !== '' ? $fullName : 'Dentist #' . $dentist->dentist_id,
                ];
            })
            ->values()
            ->all();
    }

    public function getAvailableSlots(string $date, int $serviceId, ?int $dentistId = null): array
    {
        $service = Service::query()->find($serviceId);
        $clinic = ClinicSetting::query()->first();
        $daySchedule = $this->getClinicDaySchedule($date);

        if (!$service || !$clinic || !$daySchedule || !$daySchedule['is_open']) {
            return [];
        }

        $duration = (int) $service->estimated_duration_minutes;
        $interval = max(5, (int) $clinic->slot_interval_minutes);

        if ($duration <= 0) {
            return [];
        }

        $open = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        $slots = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($duration) <= $close) {
            $startTime = $cursor->format('H:i:s');

            if ($this->isRequestedSlotAvailable($date, $startTime, $serviceId, $dentistId)) {
                $endTime = $cursor->copy()->addMinutes($duration);

                $slots[] = [
                    'start_time' => $cursor->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'label' => $cursor->format('H:i') . ' - ' . $endTime->format('H:i'),
                ];
            }

            $cursor->addMinutes($interval);
        }

        return $slots;
    }

    public function getClinicHoursForDate(string $date): array
    {
        $daySchedule = $this->getClinicDaySchedule($date);
        $clinic = ClinicSetting::query()->first();

        if (!$daySchedule || !$daySchedule['is_open'] || !$clinic) {
            return [];
        }

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $open = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        $hours = [];
        $cursor = $open->copy();

        while ($cursor < $close) {
            $hours[] = $cursor->format('H:i:s');
            $cursor->addMinutes($interval);
        }

        return $hours;
    }

    public function isRequestedSlotAvailable(
        string $date,
        string $startTime,
        int $serviceId,
        ?int $dentistId = null
    ): bool {
        $service = Service::query()->find($serviceId);
        $daySchedule = $this->getClinicDaySchedule($date);

        if (!$service || !$daySchedule || !$daySchedule['is_open']) {
            return false;
        }

        $duration = (int) $service->estimated_duration_minutes;

        if ($duration <= 0) {
            return false;
        }

        $slotStart = Carbon::parse($date . ' ' . $this->normalizeTime($startTime));
        $slotEnd = $slotStart->copy()->addMinutes($duration);

        $clinicOpen = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $clinicClose = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        if ($slotStart->lt($clinicOpen) || $slotEnd->gt($clinicClose)) {
            return false;
        }

        if ($this->hasClinicBlock($date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'))) {
            return false;
        }

        if ($dentistId !== null) {
            return $this->isDentistAvailableForSlot($dentistId, $date, $slotStart, $slotEnd);
        }

        return Dentist::query()
            ->where('is_active', 1)
            ->get()
            ->contains(function (Dentist $dentist) use ($date, $slotStart, $slotEnd) {
                return $this->isDentistAvailableForSlot(
                    (int) $dentist->dentist_id,
                    $date,
                    $slotStart,
                    $slotEnd
                );
            });
    }

    public function getClinicDaySchedule(string $date): ?array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $rule = ClinicScheduleRule::query()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$rule || !(bool) $rule->is_open) {
            return null;
        }

        return [
            'is_open' => true,
            'open_time' => $rule->open_time,
            'close_time' => $rule->close_time,
        ];
    }

    protected function hasAnyPossibleSlot(string $date, int $dentistId, int $duration): bool
    {
        $daySchedule = $this->getClinicDaySchedule($date);
        $clinic = ClinicSetting::query()->first();

        if (!$daySchedule || !$clinic || $duration <= 0) {
            return false;
        }

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $cursor = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        while ($cursor->copy()->addMinutes($duration) <= $close) {
            $start = $cursor->copy();
            $end = $cursor->copy()->addMinutes($duration);

            if ($this->isDentistAvailableForSlot($dentistId, $date, $start, $end)) {
                return true;
            }

            $cursor->addMinutes($interval);
        }

        return false;
    }

    protected function isDentistAvailableForSlot(
        int $dentistId,
        string $date,
        Carbon $slotStart,
        Carbon $slotEnd
    ): bool {
        $override = DentistDateOverride::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('override_date', $date)
            ->first();

        if ($override) {
            if (!(bool) $override->is_available) {
                return false;
            }

            if ($override->start_time && $override->end_time) {
                $overrideStart = Carbon::parse($date . ' ' . $override->start_time);
                $overrideEnd = Carbon::parse($date . ' ' . $override->end_time);

                if ($slotStart->lt($overrideStart) || $slotEnd->gt($overrideEnd)) {
                    return false;
                }
            }
        } else {
            if (!$this->isWithinDentistWeeklySchedule($dentistId, $date, $slotStart, $slotEnd)) {
                return false;
            }
        }

        if ($this->isDentistBlocked($dentistId, $date, $slotStart, $slotEnd)) {
            return false;
        }

        if ($this->hasApprovedAppointmentConflict(
            $date,
            $slotStart->format('H:i:s'),
            $slotEnd->format('H:i:s'),
            $dentistId
        )) {
            return false;
        }

        return true;
    }

    protected function isWithinDentistWeeklySchedule(
        int $dentistId,
        string $date,
        Carbon $slotStart,
        Carbon $slotEnd
    ): bool {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = DentistSchedule::query()
            ->where('dentist_id', $dentistId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', 1)
            ->first();

        if (!$schedule) {
            return false;
        }

        $scheduleStart = Carbon::parse($date . ' ' . $schedule->start_time);
        $scheduleEnd = Carbon::parse($date . ' ' . $schedule->end_time);

        return !$slotStart->lt($scheduleStart) && !$slotEnd->gt($scheduleEnd);
    }

    protected function hasClinicBlock(string $date, string $startTime, string $endTime): bool
    {
        return ScheduleBlock::query()
            ->where('scope', 'clinic')
            ->whereDate('block_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('is_full_day', true)
                    ->orWhere(function ($subQuery) use ($startTime, $endTime) {
                        $subQuery->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();
    }

    protected function isDentistBlocked(int $dentistId, string $date, Carbon $start, Carbon $end): bool
    {
        return DentistUnavailableDate::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('unavailable_date', $date)
            ->where(function ($query) use ($start, $end) {
                $query->whereNull('start_time')
                    ->orWhereNull('end_time')
                    ->orWhere(function ($subQuery) use ($start, $end) {
                        $subQuery->where('start_time', '<', $end->format('H:i:s'))
                            ->where('end_time', '>', $start->format('H:i:s'));
                    });
            })
            ->exists();
    }

    protected function hasApprovedAppointmentConflict(
        string $date,
        string $startTime,
        string $endTime,
        int $dentistId
    ): bool {
        return Appointment::query()
            ->whereDate('appointment_date', $date)
            ->where('dentist_id', $dentistId)
            ->whereIn('status', ['confirmed', 'checked_in', 'in_progress', 'completed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    protected function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time . ':00' : $time;
    }

    public function getCalendarAvailabilityForMonth(string $month, int $serviceId, ?int $dentistId = null): array
{
    $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $endOfMonth = $startOfMonth->copy()->endOfMonth();
    $today = now()->startOfDay();

    $results = [];
    $cursor = $startOfMonth->copy();

    while ($cursor->lte($endOfMonth)) {
        $date = $cursor->toDateString();

        if ($cursor->lt($today)) {
            $results[$date] = [
                'status' => 'unavailable',
                'clickable' => false,
                'label' => 'Unavailable',
            ];

            $cursor->addDay();
            continue;
        }

        $results[$date] = $this->buildCalendarDayStatus($date, $serviceId, $dentistId);
        $cursor->addDay();
    }

    return $results;
}

protected function buildCalendarDayStatus(string $date, int $serviceId, ?int $dentistId = null): array
{
    $service = Service::query()->find($serviceId);

    if (!$service || (int) $service->estimated_duration_minutes <= 0) {
        return [
            'status' => 'unavailable',
            'clickable' => false,
            'label' => 'Unavailable',
        ];
    }

    $daySchedule = $this->getClinicDaySchedule($date);
    $clinic = ClinicSetting::query()->first();

    if (!$daySchedule || !$clinic || !$daySchedule['is_open']) {
        return [
            'status' => 'unavailable',
            'clickable' => false,
            'label' => 'Unavailable',
        ];
    }

    $duration = (int) $service->estimated_duration_minutes;
    $interval = max(5, (int) $clinic->slot_interval_minutes);

    $open = Carbon::parse($date . ' ' . $daySchedule['open_time']);
    $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);
    $cursor = $open->copy();

    while ($cursor->copy()->addMinutes($duration) <= $close) {
        if ($this->isRequestedSlotAvailable($date, $cursor->format('H:i:s'), $serviceId, $dentistId)) {
            return [
                'status' => 'available',
                'clickable' => true,
                'label' => 'Available',
            ];
        }

        $cursor->addMinutes($interval);
    }

    return [
        'status' => 'unavailable',
        'clickable' => false,
        'label' => 'Unavailable',
    ];
}


}
