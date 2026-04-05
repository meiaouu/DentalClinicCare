<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\ClinicSetting;
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
            ->map(function ($dentist) {
                return [
                    'dentist_id' => $dentist->dentist_id,
                    'label' => 'Dentist #' . $dentist->dentist_id,
                ];
            })
            ->values()
            ->all();
    }

    public function getAvailableSlots(string $date, int $serviceId, ?int $dentistId = null): array
    {
        $clinic = ClinicSetting::query()->firstOrFail();
        $service = Service::query()->findOrFail($serviceId);

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $duration = (int) $service->estimated_duration_minutes;

        $slots = [];
        $dayStart = Carbon::parse($date . ' ' . $clinic->open_time);
        $dayEnd = Carbon::parse($date . ' ' . $clinic->close_time);

        $cursor = $dayStart->copy();

        while ($cursor->copy()->addMinutes($duration) <= $dayEnd) {
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

    public function isRequestedSlotAvailable(
        string $date,
        string $startTime,
        int $serviceId,
        ?int $dentistId = null
    ): bool {
        $clinic = ClinicSetting::query()->firstOrFail();
        $service = Service::query()->findOrFail($serviceId);

        $duration = (int) $service->estimated_duration_minutes;

        $slotStart = Carbon::parse($date . ' ' . $startTime);
        $slotEnd = $slotStart->copy()->addMinutes($duration);

        $clinicOpen = Carbon::parse($date . ' ' . $clinic->open_time);
        $clinicClose = Carbon::parse($date . ' ' . $clinic->close_time);

        if ($slotStart->lt($clinicOpen) || $slotEnd->gt($clinicClose)) {
            return false;
        }

        if ($dentistId !== null) {
            if ($this->isDentistBlocked($dentistId, $date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'))) {
                return false;
            }

            if ($this->hasConfirmedAppointmentConflict($date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'), $dentistId)) {
                return false;
            }

            return true;
        }

        $availableDentists = Dentist::query()
            ->where('is_active', 1)
            ->get()
            ->filter(function ($dentist) use ($date, $slotStart, $slotEnd) {
                if ($this->isDentistBlocked($dentist->dentist_id, $date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'))) {
                    return false;
                }

                if ($this->hasConfirmedAppointmentConflict($date, $slotStart->format('H:i:s'), $slotEnd->format('H:i:s'), $dentist->dentist_id)) {
                    return false;
                }

                return true;
            });

        return $availableDentists->isNotEmpty();
    }

    protected function hasAnyPossibleSlot(string $date, int $dentistId, int $duration): bool
    {
        $clinic = ClinicSetting::query()->first();

        if (!$clinic) {
            return false;
        }

        $interval = max(5, (int) $clinic->slot_interval_minutes);
        $cursor = Carbon::parse($date . ' ' . $clinic->open_time);
        $dayEnd = Carbon::parse($date . ' ' . $clinic->close_time);

        while ($cursor->copy()->addMinutes($duration) <= $dayEnd) {
            $start = $cursor->format('H:i:s');
            $end = $cursor->copy()->addMinutes($duration)->format('H:i:s');

            if (
                !$this->isDentistBlocked($dentistId, $date, $start, $end) &&
                !$this->hasConfirmedAppointmentConflict($date, $start, $end, $dentistId)
            ) {
                return true;
            }

            $cursor->addMinutes($interval);
        }

        return false;
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

        $unavailableDateExists = DentistUnavailableDate::query()
            ->where('dentist_id', $dentistId)
            ->whereDate('unavailable_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        return $unavailableDateExists;
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
