<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\DentistDateOverride;
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

        $rawSchedules = DentistSchedule::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy(function ($schedule) {
                return (int) $schedule->day_of_week;
            });

        $dayLabels = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $defaultSchedules = collect([
            0 => (object) [
                'day_of_week' => 0,
                'is_available' => 0,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            1 => (object) [
                'day_of_week' => 1,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            2 => (object) [
                'day_of_week' => 2,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            3 => (object) [
                'day_of_week' => 3,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            4 => (object) [
                'day_of_week' => 4,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            5 => (object) [
                'day_of_week' => 5,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
            6 => (object) [
                'day_of_week' => 6,
                'is_available' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'max_patients' => 20,
            ],
        ]);

        $schedules = $defaultSchedules->merge($rawSchedules);

        $unavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('unavailable_date')
            ->paginate(15, ['*'], 'blocked_page');

        $dateOverrides = DentistDateOverride::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('override_date')
            ->paginate(10, ['*'], 'override_page');

        $dateOverridesMap = DentistDateOverride::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->get()
            ->keyBy('override_date');

        $currentMonthStart = now()->startOfMonth();
$currentMonthEnd = now()->endOfMonth();

$latestSchedules = DentistSchedule::query()
    ->where('dentist_id', $dentist->dentist_id)
    ->get()
    ->groupBy('day_of_week')
    ->map(function ($rows) {
        return $rows->sortByDesc('updated_at')->first();
    });

$monthOverrides = DentistDateOverride::query()
    ->where('dentist_id', $dentist->dentist_id)
    ->whereBetween('override_date', [
        $currentMonthStart->toDateString(),
        $currentMonthEnd->toDateString(),
    ])
    ->get()
    ->keyBy('override_date');

$monthBlockedDates = DentistUnavailableDate::query()
    ->where('dentist_id', $dentist->dentist_id)
    ->whereBetween('unavailable_date', [
        $currentMonthStart->toDateString(),
        $currentMonthEnd->toDateString(),
    ])
    ->get()
    ->keyBy('unavailable_date');

$availableDaysCount = 0;
$unavailableDaysCount = 0;

$cursor = $currentMonthStart->copy();

while ($cursor->lte($currentMonthEnd)) {
    $dateKey = $cursor->toDateString();

    $override = $monthOverrides->get($dateKey);
    $blockedDate = $monthBlockedDates->get($dateKey);
    $dayOfWeek = $cursor->dayOfWeek; // 0 = Sunday

    if ($override) {
        if ((bool) $override->is_available) {
            $availableDaysCount++;
        } else {
            $unavailableDaysCount++;
        }
    } elseif ($blockedDate) {
        $unavailableDaysCount++;
    } elseif ($dayOfWeek === 0) {
        $unavailableDaysCount++;
    } else {
        $availableDaysCount++;
    }

    $cursor->addDay();
}

$summary = [
    'available_days' => $availableDaysCount,
    'unavailable_days' => $unavailableDaysCount,

    'date_blocks' => DentistUnavailableDate::query()
        ->where('dentist_id', $dentist->dentist_id)
        ->whereBetween('unavailable_date', [
            $currentMonthStart->toDateString(),
            $currentMonthEnd->toDateString(),
        ])
        ->count(),

    'weekly_entries' => $latestSchedules->count(),

    'available_overrides' => DentistDateOverride::query()
        ->where('dentist_id', $dentist->dentist_id)
        ->where('is_available', true)
        ->count(),

    'unavailable_overrides' => DentistDateOverride::query()
        ->where('dentist_id', $dentist->dentist_id)
        ->where('is_available', false)
        ->count(),
];

        return view('dentist.availability.index', compact(
            'dentist',
            'schedules',
            'unavailableDates',
            'dateOverrides',
            'dateOverridesMap',
            'dayLabels',
            'summary'
        ));
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
                        'day_of_week' => (int) $day,
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

    public function storeDateOverride(Request $request): RedirectResponse
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist, 403);

        $validated = $request->validate([
            'override_date' => ['required', 'date', 'after_or_equal:today'],
            'is_available' => ['required', 'boolean'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DentistDateOverride::updateOrCreate(
            [
                'dentist_id' => $dentist->dentist_id,
                'override_date' => $validated['override_date'],
            ],
            [
                'is_available' => (bool) $validated['is_available'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'reason' => $validated['reason'] ?? null,
            ]
        );

        return back()->with('success', 'Date override saved successfully.');
    }

    public function destroyDateOverride(DentistDateOverride $dateOverride): RedirectResponse
    {
        $dentist = Auth::user()->dentist;
        abort_if(!$dentist || $dateOverride->dentist_id !== $dentist->dentist_id, 403);

        $dateOverride->delete();

        return back()->with('success', 'Date override removed successfully.');
    }
}
