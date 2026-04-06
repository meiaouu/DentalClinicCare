<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ClinicScheduleBlock;
use App\Models\ClinicWeeklySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClinicScheduleController extends Controller
{
    public function index(): View
    {
        $weeklySchedules = ClinicWeeklySchedule::orderBy('day_of_week')->get();
        $blocks = ClinicScheduleBlock::orderByDesc('block_date')->get();

        return view('staff.clinic-schedule.index', compact('weeklySchedules', 'blocks'));
    }

    public function openSpecificDate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
        ]);

        ClinicScheduleBlock::query()
            ->whereDate('block_date', $validated['date'])
            ->where('reason', 'Reserved Sunday Opening')
            ->delete();

        ClinicScheduleBlock::create([
            'block_date' => $validated['date'],
            'start_time' => null,
            'end_time' => null,
            'is_full_day' => false,
            'reason' => 'Reserved Sunday Opening',
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        $dayName = strtolower(\Carbon\Carbon::parse($validated['date'])->format('l'));

        ClinicWeeklySchedule::updateOrCreate(
            ['day_of_week' => $dayName],
            [
                'is_open' => true,
                'open_time' => $validated['open_time'],
                'close_time' => $validated['close_time'],
                'is_reserve_only' => $dayName === 'sunday',
            ]
        );

        return back()->with('success', 'Specific date opened successfully.');
    }

    public function blockDateOrTime(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'block_date' => ['required', 'date'],
            'is_full_day' => ['required', 'boolean'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        ClinicScheduleBlock::create([
            'block_date' => $validated['block_date'],
            'start_time' => $validated['is_full_day'] ? null : ($validated['start_time'] ?? null),
            'end_time' => $validated['is_full_day'] ? null : ($validated['end_time'] ?? null),
            'is_full_day' => (bool) $validated['is_full_day'],
            'reason' => $validated['reason'] ?? 'Staff block',
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Clinic block created successfully.');
    }
}
