<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\ClinicScheduleRule;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\DentistSchedule;
use App\Models\DentistUnavailableDate;
use App\Models\ScheduleBlock;
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
                    (int) $dentist->dentist_id,
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
            $startTime = $cursor->format('H:i:s');
            $endTime = $cursor->copy()->addMinutes($duration)->format('H:i:s');

            if ($this->isRequestedSlotAvailable($date, $startTime, $serviceId, $dentistId)) {
                $slots[] = [
                    'start_time' => $cursor->format('H:i'),
                    'end_time' => Carbon::parse($date . ' ' . $endTime)->format('H:i'),
                    'label' => $cursor->format('H:i') . ' - ' . Carbon::parse($date . ' ' . $endTime)->format('H:i'),
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
            if (!$this->isDentistScheduled(
                $dentistId,
                $date,
                $slotStart->format('H:i:s'),
                $slotEnd->format('H:i:s')
            )) {
                return false;
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

        return Dentist::query()
            ->where('is_active', 1)
            ->get()
            ->contains(function ($dentist) use ($date, $slotStart, $slotEnd) {
                $dentistId = (int) $dentist->dentist_id;

                if (!$this->isDentistScheduled(
                    $dentistId,
                    $date,
                    $slotStart->format('H:i:s'),
                    $slotEnd->format('H:i:s')
                )) {
                    return false;
                }

                if ($this->isDentistBlocked($dentistId, $date, $slotStart, $slotEnd)) {
                    return false;
                }

                return !$this->hasApprovedAppointmentConflict(
                    $date,
                    $slotStart->format('H:i:s'),
                    $slotEnd->format('H:i:s'),
                    $dentistId
                );
            });
    }

    public function getClinicDaySchedule(string $date): ?array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $rule = ClinicScheduleRule::query()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$rule || !$rule->is_open) {
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

        if (!$daySchedule || !$clinic) {
            return false;
        }

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $cursor = Carbon::parse($date . ' ' . $daySchedule['open_time']);
        $close = Carbon::parse($date . ' ' . $daySchedule['close_time']);

        while ($cursor->copy()->addMinutes($duration) <= $close) {
            $start = $cursor->copy();
            $end = $cursor->copy()->addMinutes($duration);

            if (
                $this->isDentistScheduled(
                    $dentistId,
                    $date,
                    $start->format('H:i:s'),
                    $end->format('H:i:s')
                ) &&
                !$this->hasClinicBlock($date, $start->format('H:i:s'), $end->format('H:i:s')) &&
                !$this->isDentistBlocked($dentistId, $date, $start, $end) &&
                !$this->hasApprovedAppointmentConflict(
                    $date,
                    $start->format('H:i:s'),
                    $end->format('H:i:s'),
                    $dentistId
                )
            ) {
                return true;
            }

            $cursor->addMinutes($interval);
        }

        return false;
    }

    protected function hasClinicBlock(string $date, string $startTime, string $endTime): bool
    {
        return ScheduleBlock::query()
            ->where('scope', 'clinic')
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

    protected function isDentistScheduled(int $dentistId, string $date, string $startTime, string $endTime): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        return DentistSchedule::query()
            ->where('dentist_id', $dentistId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();
    }

    protected function isDentistBlocked(int $dentistId, string $date, Carbon $start, Carbon $end): bool
    {
        return DentistUnavailableDate::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('unavailable_date', $date)
            ->where(function ($q) use ($start, $end) {
                $q->whereNull('start_time')
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('start_time', '<', $end->format('H:i:s'))
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
            ->whereIn('status', ['approved', 'completed', 'checked_in', 'in_progress'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
