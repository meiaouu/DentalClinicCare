<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\DentistDateOverride;
use App\Models\DentistSchedule;
use App\Models\DentistUnavailableDate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\ClinicSetting;

class AvailabilityController extends Controller
{
    protected array $dayLabels = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function index(Request $request): View
    {
        $dentist = $this->getAuthenticatedDentist();

        $calendarMonth = $request->input('month');
        $monthBase = $calendarMonth
            ? Carbon::createFromFormat('Y-m', $calendarMonth)->startOfMonth()
            : now()->startOfMonth();

        $monthStart = $monthBase->copy()->startOfMonth();
        $monthEnd = $monthBase->copy()->endOfMonth();

        $schedules = $this->getMergedSchedules($dentist->dentist_id);

        $unavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('unavailable_date')
            ->paginate(15, ['*'], 'blocked_page');

        $dateOverrides = DentistDateOverride::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->orderByDesc('override_date')
            ->paginate(10, ['*'], 'override_page');

        $monthlyUnavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->whereBetween('unavailable_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->unavailable_date)->toDateString());

        $monthlyDateOverrides = DentistDateOverride::query()
            ->where('dentist_id', $dentist->dentist_id)
            ->whereBetween('override_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->override_date)->toDateString());

        $summary = $this->buildMonthlySummary(
            dentistId: (int) $dentist->dentist_id,
            schedules: $schedules,
            monthStart: $monthStart,
            monthEnd: $monthEnd
        );

        return view('dentist.availability.index', [
            'dentist' => $dentist,
            'schedules' => $schedules,
            'unavailableDates' => $unavailableDates,
            'dateOverrides' => $dateOverrides,
            'monthlyUnavailableDates' => $monthlyUnavailableDates,
            'monthlyDateOverrides' => $monthlyDateOverrides,
            'dayLabels' => $this->dayLabels,
            'summary' => $summary,
            'calendarMonthStart' => $monthStart,
            'calendarMonth' => $monthStart->format('Y-m'),
        ]);
    }

    public function storeOrUpdate(Request $request): RedirectResponse
    {
        $dentist = $this->getAuthenticatedDentist();

        $validated = $request->validate([
            'days' => ['required', 'array'],
            'days.*.is_available' => ['nullable', 'boolean'],
            'days.*.start_time' => ['nullable', 'date_format:H:i'],
            'days.*.end_time' => ['nullable', 'date_format:H:i'],
            'days.*.max_patients' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $dentist) {
            foreach ($this->dayLabels as $day => $label) {
                $row = $validated['days'][$day] ?? [];

                $isAvailable = (bool) ($row['is_available'] ?? false);
                $startTime = $row['start_time'] ?? null;
                $endTime = $row['end_time'] ?? null;
                $maxPatients = isset($row['max_patients']) ? (int) $row['max_patients'] : 20;

                if ($isAvailable) {
                    if (!$startTime || !$endTime) {
                        abort(422, "{$label} must have both start and end time when marked available.");
                    }

                    if (strtotime($endTime) <= strtotime($startTime)) {
                        abort(422, "{$label} end time must be after start time.");
                    }
                }

                DentistSchedule::updateOrCreate(
                    [
                        'dentist_id' => $dentist->dentist_id,
                        'day_of_week' => $day,
                    ],
                    [
                        'is_available' => $isAvailable,
                        'start_time' => $isAvailable ? $startTime . ':00' : null,
                        'end_time' => $isAvailable ? $endTime . ':00' : null,
                        'max_patients' => $maxPatients,
                    ]
                );
            }
        });

        return back()->with('success', 'Weekly availability updated successfully.');
    }

    public function storeUnavailableDate(Request $request): RedirectResponse
    {
        $dentist = $this->getAuthenticatedDentist();

        $validated = $request->validate([
            'unavailable_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if (
            !empty($validated['start_time']) &&
            !empty($validated['end_time']) &&
            strtotime($validated['end_time']) <= strtotime($validated['start_time'])
        ) {
            return back()
                ->withErrors(['end_time' => 'End time must be after start time.'])
                ->withInput();
        }

        DentistUnavailableDate::create([
            'dentist_id' => $dentist->dentist_id,
            'unavailable_date' => $validated['unavailable_date'],
            'start_time' => !empty($validated['start_time']) ? $validated['start_time'] . ':00' : null,
            'end_time' => !empty($validated['end_time']) ? $validated['end_time'] . ':00' : null,
            'reason' => $validated['reason'] ?? 'Dentist unavailable',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Unavailable date saved successfully.');
    }

    public function destroyUnavailableDate(DentistUnavailableDate $unavailableDate): RedirectResponse
    {
        $dentist = $this->getAuthenticatedDentist();

        abort_if((int) $unavailableDate->dentist_id !== (int) $dentist->dentist_id, 403);

        $unavailableDate->delete();

        return back()->with('success', 'Unavailable date removed successfully.');
    }

    public function storeDateOverride(Request $request): RedirectResponse
{
    $dentist = $this->getAuthenticatedDentist();

    $validated = $request->validate([
        'override_date' => ['required', 'date', 'after_or_equal:today'],
        'is_available' => ['required', 'boolean'],
        'availability_mode' => ['nullable', 'in:full_day,morning,afternoon,custom'],
        'start_time' => ['nullable', 'date_format:H:i'],
        'end_time' => ['nullable', 'date_format:H:i'],
        'reason' => ['nullable', 'string', 'max:255'],
    ]);

    $isAvailable = (bool) $validated['is_available'];
    $mode = $validated['availability_mode'] ?? null;

    $startTime = null;
    $endTime = null;

    if ($isAvailable) {
        if ($mode === 'custom') {
            $startTime = $validated['start_time'] ?? null;
            $endTime = $validated['end_time'] ?? null;

            if (!$startTime || !$endTime) {
                return back()
                    ->withErrors(['start_time' => 'Custom available override must include both start and end time.'])
                    ->withInput();
            }
        } else {
            [$startTime, $endTime] = $this->resolveQuickAvailabilityRange(
                dentistId: (int) $dentist->dentist_id,
                date: $validated['override_date'],
                mode: $mode ?: 'full_day'
            );

            if (!$startTime || !$endTime) {
                return back()
                    ->withErrors([
                        'override_date' => 'No valid working range was found for that date. Please set the weekly schedule first or use custom time range.',
                    ])
                    ->withInput();
            }
        }

        if (strtotime($endTime) <= strtotime($startTime)) {
            return back()
                ->withErrors(['end_time' => 'End time must be after start time.'])
                ->withInput();
        }
    }

    DentistDateOverride::updateOrCreate(
        [
            'dentist_id' => $dentist->dentist_id,
            'override_date' => $validated['override_date'],
        ],
        [
            'is_available' => $isAvailable,
            'start_time' => $isAvailable ? $startTime . ':00' : null,
            'end_time' => $isAvailable ? $endTime . ':00' : null,
            'reason' => $validated['reason'] ?? $this->buildOverrideReason($isAvailable, $mode),
        ]
    );

    return back()->with('success', 'Date override saved successfully.');
}


protected function resolveQuickAvailabilityRange(int $dentistId, string $date, string $mode): array
{
    $dateCarbon = Carbon::parse($date);
    $dayOfWeek = $dateCarbon->dayOfWeek;

    $schedule = DentistSchedule::query()
        ->where('dentist_id', $dentistId)
        ->where('day_of_week', $dayOfWeek)
        ->where('is_available', 1)
        ->first();

    $range = $this->resolveBaseWorkingRange($date, $schedule);

    if (!$range) {
        return [null, null];
    }

    [$start, $end] = $range;

    if ($end->lte($start)) {
        return [null, null];
    }

    $morningEnd = $this->resolveMorningEnd($date, $start, $end);
    $afternoonStart = $this->resolveAfternoonStart($date, $start, $end, $morningEnd);

    return match ($mode) {
        'full_day' => [$start->format('H:i'), $end->format('H:i')],
        'morning' => [$start->format('H:i'), $morningEnd->format('H:i')],
        'afternoon' => [$afternoonStart->format('H:i'), $end->format('H:i')],
        default => [null, null],
    };
}

protected function resolveBaseWorkingRange(string $date, ?DentistSchedule $schedule = null): ?array
{
    if ($schedule && $schedule->start_time && $schedule->end_time) {
        $start = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        if ($end->gt($start)) {
            return [$start, $end];
        }
    }

    $clinic = ClinicSetting::query()->first();

    if ($clinic && !empty($clinic->open_time) && !empty($clinic->close_time)) {
        $start = Carbon::parse($date . ' ' . $clinic->open_time);
        $end = Carbon::parse($date . ' ' . $clinic->close_time);

        if ($end->gt($start)) {
            return [$start, $end];
        }
    }

    // Final safe fallback defaults
    $defaultStart = Carbon::parse($date . ' 08:00:00');
    $defaultEnd = Carbon::parse($date . ' 17:00:00');

    if ($defaultEnd->gt($defaultStart)) {
        return [$defaultStart, $defaultEnd];
    }

    return null;
}



protected function resolveMorningEnd(string $date, Carbon $start, Carbon $end): Carbon
{
    $defaultMorningEnd = Carbon::parse($date . ' 12:00:00');

    if ($defaultMorningEnd->gt($start) && $defaultMorningEnd->lt($end)) {
        return $defaultMorningEnd;
    }

    return $start->copy()->addMinutes((int) floor($start->diffInMinutes($end) / 2));
}

protected function resolveAfternoonStart(
    string $date,
    Carbon $start,
    Carbon $end,
    Carbon $morningEnd
): Carbon {
    $defaultAfternoonStart = Carbon::parse($date . ' 13:00:00');

    if ($defaultAfternoonStart->gt($start) && $defaultAfternoonStart->lt($end)) {
        return $defaultAfternoonStart;
    }

    return $morningEnd->copy();
}



protected function buildOverrideReason(bool $isAvailable, ?string $mode): string
{
    if (!$isAvailable) {
        return 'Unavailable override';
    }

    return match ($mode) {
        'morning' => 'Half day - morning',
        'afternoon' => 'Half day - afternoon',
        'custom' => 'Custom available override',
        default => 'Available override',
    };
}

    public function destroyDateOverride(DentistDateOverride $dateOverride): RedirectResponse
    {
        $dentist = $this->getAuthenticatedDentist();

        abort_if((int) $dateOverride->dentist_id !== (int) $dentist->dentist_id, 403);

        $dateOverride->delete();

        return back()->with('success', 'Date override removed successfully.');
    }

    protected function getAuthenticatedDentist(): object
    {
        $dentist = Auth::user()?->dentist;

        abort_if(!$dentist, 403);

        return $dentist;
    }

    protected function getMergedSchedules(int $dentistId): Collection
    {
        $rawSchedules = DentistSchedule::query()
            ->where('dentist_id', $dentistId)
            ->get()
            ->keyBy(fn ($schedule) => (int) $schedule->day_of_week);

        $defaultSchedules = collect([
            0 => (object) ['day_of_week' => 0, 'is_available' => 0, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            1 => (object) ['day_of_week' => 1, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            2 => (object) ['day_of_week' => 2, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            3 => (object) ['day_of_week' => 3, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            4 => (object) ['day_of_week' => 4, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            5 => (object) ['day_of_week' => 5, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
            6 => (object) ['day_of_week' => 6, 'is_available' => 1, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'max_patients' => 20],
        ]);

        return $defaultSchedules->merge($rawSchedules)->sortKeys();
    }

    protected function buildMonthlySummary(
        int $dentistId,
        Collection $schedules,
        Carbon $monthStart,
        Carbon $monthEnd
    ): array {
        $monthOverrides = DentistDateOverride::query()
            ->where('dentist_id', $dentistId)
            ->whereBetween('override_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($row) => (string) $row->override_date);

        $monthUnavailableDates = DentistUnavailableDate::query()
            ->where('dentist_id', $dentistId)
            ->whereBetween('unavailable_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($row) => (string) $row->unavailable_date);

        $availableDaysCount = 0;
        $unavailableDaysCount = 0;
        $cursor = $monthStart->copy();

        while ($cursor->lte($monthEnd)) {
            $dateKey = $cursor->toDateString();
            $dayOfWeek = $cursor->dayOfWeek;

            $override = $monthOverrides->get($dateKey);
            $blockedDate = $monthUnavailableDates->get($dateKey);
            $weeklySchedule = $schedules->get($dayOfWeek);

            if ($override) {
                (bool) $override->is_available ? $availableDaysCount++ : $unavailableDaysCount++;
            } elseif ($blockedDate) {
                $unavailableDaysCount++;
            } elseif ($weeklySchedule && (int) $weeklySchedule->is_available === 1) {
                $availableDaysCount++;
            } else {
                $unavailableDaysCount++;
            }

            $cursor->addDay();
        }

        return [
            'month' => $monthStart->format('F Y'),
            'available_days' => $availableDaysCount,
            'unavailable_days' => $unavailableDaysCount,
            'date_blocks' => $monthUnavailableDates->count(),
            'weekly_entries' => $schedules->count(),
            'available_overrides' => DentistDateOverride::query()
                ->where('dentist_id', $dentistId)
                ->where('is_available', true)
                ->count(),
            'unavailable_overrides' => DentistDateOverride::query()
                ->where('dentist_id', $dentistId)
                ->where('is_available', false)
                ->count(),
        ];
    }
}
