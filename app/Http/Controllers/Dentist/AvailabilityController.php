<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\DentistSchedule;
use App\Models\DentistUnavailableDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $dentist = Auth::user()?->dentist;

        abort_unless($dentist, 403, 'Dentist profile not found.');

        $schedules = DentistSchedule::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $unavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('unavailable_date')
            ->get();

        return view('dentist.availability.index', [
            'dentist' => $dentist,
            'schedules' => $schedules,
            'unavailableDates' => $unavailableDates,
            'dayLabels' => [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ],
        ]);
    }

    public function storeOrUpdate(Request $request): RedirectResponse
    {
        $dentist = Auth::user()?->dentist;

        abort_unless($dentist, 403, 'Dentist profile not found.');

        $validated = $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.day_of_week' => ['required', 'string'],
            'schedules.*.is_available' => ['nullable', 'in:0,1'],
            'schedules.*.start_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.end_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.max_patients' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        foreach ($validated['schedules'] as $schedule) {
            $isAvailable = (int) ($schedule['is_available'] ?? 0) === 1;

            DentistSchedule::updateOrCreate(
                [
                    'dentist_id' => $dentist->dentist_id,
                    'day_of_week' => strtolower($schedule['day_of_week']),
                ],
                [
                    'start_time' => $isAvailable ? ($schedule['start_time'] ?? null) : null,
                    'end_time' => $isAvailable ? ($schedule['end_time'] ?? null) : null,
                    'max_patients' => (int) ($schedule['max_patients'] ?? 20),
                    'is_available' => $isAvailable,
                ]
            );
        }

        return back()->with('success', 'Availability updated successfully.');
    }

    public function storeUnavailableDate(Request $request): RedirectResponse
    {
        $dentist = Auth::user()?->dentist;

        abort_unless($dentist, 403, 'Dentist profile not found.');

        $validated = $request->validate([
            'unavailable_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DentistUnavailableDate::create([
            'dentist_id' => $dentist->dentist_id,
            'unavailable_date' => $validated['unavailable_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Unavailable date added successfully.');
    }

    public function destroyUnavailableDate(DentistUnavailableDate $unavailableDate): RedirectResponse
    {
        $dentist = Auth::user()?->dentist;

        abort_unless($dentist, 403, 'Dentist profile not found.');
        abort_unless((int) $unavailableDate->dentist_id === (int) $dentist->dentist_id, 403);

        $unavailableDate->delete();

        return back()->with('success', 'Unavailable date removed successfully.');
    }
}
