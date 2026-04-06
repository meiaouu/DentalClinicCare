<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\ClinicScheduleBlock;
use App\Models\ClinicSetting;
use App\Models\ClinicWeeklySchedule;
use App\Models\Dentist;
use App\Models\DentistScheduleBlock;
use App\Models\DentistUnavailableDate;
use App\Models\Service;
use Carbon\Carbon;

class BookingAvailabilityService
{
    public function getAvailableDentists(string $date, int $serviceId): array
    {
        $service = Service::query()->findOrFail($serviceId);

        return Dentist::query()
            ->where('is_active', 1)
            ->get()
            ->filter(function ($dentist) use ($date, $service) {
                return $this->hasAnyPossibleSlot(
                    $date,
                    $dentist->dentist_id,
                    (int) $service->estimated_duration_minutes
                );
            })
            ->map(fn ($dentist) => [
                'dentist_id' => $dentist->dentist_id,
                'label' => 'Dentist #' . $dentist->dentist_id,
            ])
            ->values()
            ->all();
    }

    public function getAvailableSlots(string $date, int $serviceId, ?int $dentistId = null): array
    {
        $daySchedule = $this->getClinicDaySchedule($date);

        if (!$daySchedule || !$daySchedule['is_open']) {
            return [];
        }

        $service = Service::query()->find($serviceId);
        $clinic = ClinicSetting::query()->first();

        if (!$service || !$clinic) {
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
            $startTime = $cursor->format('H:i');
            $endTime = $cursor->copy()->addMinutes($duration)->format('H:i');

            if ($this->isRequestedSlotAvailable($date, $startTime, $serviceId, $dentistId)) {
                $slots[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'label' => $startTime . ' - ' . $endTime,
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
            $hours[] = $cursor->format('H:i');
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
        $daySchedule = $this->getClinicDaySchedule($date);

        if (!$daySchedule || !$daySchedule['is_open']) {
            return false;
        }

        $service = Service::query()->find($serviceId);

        if (!$service || (int) $service->estimated_duration_minutes <= 0) {
            return false;
        }

        $duration = (int) $service->estimated_duration_minutes;

        $slotStart = Carbon::parse($date . ' ' . $startTime);
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
            if ($this->isDentistBlocked($dentistId, $date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'))) {
                return false;
            }

            if ($this->hasConfirmedAppointmentConflict(
                $date,
                $slotStart->format('H:i:s'),
                $slotEnd->format('H:i:s'),
                $dentistId
            )) {
                return false;
            }

            return true;
        }

        $hasAnyDentist = Dentist::query()
            ->where('is_active', 1)
            ->get()
            ->contains(function ($dentist) use ($date, $slotStart, $slotEnd) {
                if ($this->isDentistBlocked(
                    $dentist->dentist_id,
                    $date,
                    $slotStart->format('H:i:s'),
                    $slotEnd->format('H:i:s')
                )) {
                    return false;
                }

                return !$this->hasConfirmedAppointmentConflict(
                    $date,
                    $slotStart->format('H:i:s'),
                    $slotEnd->format('H:i:s'),
                    $dentist->dentist_id
                );
            });

        return $hasAnyDentist;
    }

    public function getClinicDaySchedule(string $date): ?array
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));

        $weekly = ClinicWeeklySchedule::query()
            ->where('day_of_week', $dayName)
            ->first();

        if (!$weekly || !$weekly->is_open) {
            return null;
        }

        return [
            'is_open' => true,
            'open_time' => $weekly->open_time,
            'close_time' => $weekly->close_time,
            'is_reserve_only' => $weekly->is_reserve_only,
        ];
    }

    protected function hasAnyPossibleSlot(string $date, int $dentistId, int $duration): bool
    {
        $daySchedule = $this->getClinicDaySchedule($date);
        $clinic = ClinicSetting::query()->first();

        if (!$daySchedule || !$clinic) {
            return false;
        }

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $cursor = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        while ($cursor->copy()->addMinutes($duration) <= $close) {
            $start = $cursor->format('H:i:s');
            $end = $cursor->copy()->addMinutes($duration)->format('H:i:s');

            if (
                !$this->hasClinicBlock($date, $start, $end) &&
                !$this->isDentistBlocked($dentistId, $date, $start, $end) &&
                !$this->hasConfirmedAppointmentConflict($date, $start, $end, $dentistId)
            ) {
                return true;
            }

            $cursor->addMinutes($interval);
        }

        return false;
    }

    protected function hasClinicBlock(string $date, string $startTime, string $endTime): bool
    {
        return ClinicScheduleBlock::query()
            ->whereDate('block_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('is_full_day', true)
                  ->orWhere(function ($sub) use ($startTime, $endTime) {
                      $sub->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                  });
            })
            ->exists();
    }

    protected function isDentistBlocked(int $dentistId, string $date, string $startTime, string $endTime): bool
    {
        $scheduleBlockExists = DentistScheduleBlock::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('block_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($scheduleBlockExists) {
            return true;
        }

        return DentistUnavailableDate::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('unavailable_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    protected function hasConfirmedAppointmentConflict(
        string $date,
        string $startTime,
        string $endTime,
        int $dentistId
    ): bool {
        return Appointment::query()
            ->whereDate('appointment_date', $date)
            ->where('dentist_id', $dentistId)
            ->whereIn('status', ['confirmed', 'checked_in', 'in_progress'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
