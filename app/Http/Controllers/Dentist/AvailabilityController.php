<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\DentistSchedule;
use App\Models\DentistUnavailableDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist, 403);

        $schedules = DentistSchedule::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $unavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('unavailable_date')
            ->paginate(15);

        $dayLabels = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('dentist.availability.index', compact('dentist', 'schedules', 'unavailableDates', 'dayLabels'));
    }

    public function storeOrUpdate(Request $request): RedirectResponse
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist, 403);

        $validated = $request->validate([
            'days' => ['required', 'array'],
            'days.*.is_available' => ['nullable', 'boolean'],
            'days.*.start_time' => ['nullable', 'date_format:H:i'],
            'days.*.end_time' => ['nullable', 'date_format:H:i', 'after:days.*.start_time'],
            'days.*.max_patients' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $dentist) {
            foreach ($validated['days'] as $day => $row) {
                $isAvailable = (bool) ($row['is_available'] ?? false);

                DentistSchedule::updateOrCreate(
                    [
                        'dentist_id' => $dentist->dentist_id,
                        'day_of_week' => strtolower($day),
                    ],
                    [
                        'start_time' => $isAvailable ? ($row['start_time'] ?? null) : null,
                        'end_time' => $isAvailable ? ($row['end_time'] ?? null) : null,
                        'max_patients' => $row['max_patients'] ?? 20,
                        'is_available' => $isAvailable,
                    ]
                );
            }
        });

        return back()->with('success', 'Weekly availability updated successfully.');
    }

    public function storeUnavailableDate(Request $request): RedirectResponse
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist, 403);

        $validated = $request->validate([
            'unavailable_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DentistUnavailableDate::create([
            'dentist_id' => $dentist->dentist_id,
            'unavailable_date' => $validated['unavailable_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'reason' => $validated['reason'] ?? 'Dentist unavailable',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Unavailable date saved successfully.');
    }

    public function destroyUnavailableDate(DentistUnavailableDate $unavailableDate): RedirectResponse
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist || $unavailableDate->dentist_id !== $dentist->dentist_id, 403);

        $unavailableDate->delete();

        return back()->with('success', 'Unavailable date removed successfully.');
    }
}
