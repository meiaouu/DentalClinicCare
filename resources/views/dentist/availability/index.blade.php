@extends('dentist.layouts.app')

@section('page_title', 'Availability')

@section('dentist_content')
<style>
    .availability-page {
        display: grid;
        gap: 20px;
    }

    .availability-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .availability-title {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .availability-subtitle {
        margin: 8px 0 0;
        max-width: 760px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .availability-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) 340px;
        gap: 20px;
        align-items: start;
    }

    .availability-left,
    .availability-right {
        display: grid;
        gap: 18px;
    }

    .availability-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .availability-panel-body {
        padding: 20px;
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .section-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    .section-text {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .calendar-card-top {
        padding: 20px;
        border-bottom: 1px solid #eef2f7;
        background: #ffffff;
    }

    .calendar-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .calendar-toolbar-left,
    .calendar-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .calendar-btn {
        border: 1px solid #dbe4ea;
        background: #ffffff;
        color: #334155;
        border-radius: 12px;
        padding: 9px 12px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .calendar-btn.active {
        background: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
    }

    .calendar-title {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .selected-date-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .selected-date-label {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
    }

    .selected-date-value {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #0f766e;
        font-size: 13px;
        font-weight: 800;
    }

    .calendar-action-box {
        display: none;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #dbe4ea;
        border-radius: 14px;
        background: #ffffff;
    }

    .calendar-action-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .calendar-action-caption {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
    }

    .calendar-action-date {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 4px;
    }

    .calendar-action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .calendar-box {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
    }

    .calendar-weekdays,
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .calendar-weekday {
        padding: 12px 8px;
        text-align: center;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
    }

    .calendar-cell {
        min-height: 100px;
        padding: 10px;
        border-right: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
        background: #ffffff;
        cursor: pointer;
        transition: background 0.18s ease, box-shadow 0.18s ease;
    }

    .calendar-cell:nth-child(7n) {
        border-right: none;
    }

    .calendar-cell:hover {
        background: #f8fafc;
    }

    .calendar-cell.muted {
        background: #fbfdff;
        cursor: not-allowed;
    }

    .calendar-cell.muted:hover {
        background: #fbfdff;
    }

    .calendar-cell.weekly-available {
        background: #f8fffb;
    }

    .calendar-cell.override-available {
        background: #ecfdf5;
    }

    .calendar-cell.override-unavailable {
        background: #fef2f2;
    }

    .calendar-cell.selected {
        box-shadow: inset 0 0 0 2px #0f766e;
    }

    .calendar-date {
        font-size: 13px;
        font-weight: 800;
        color: #334155;
        margin-bottom: 8px;
    }

    .calendar-cell.muted .calendar-date {
        color: #cbd5e1;
    }

    .calendar-mini-tag {
        display: inline-flex;
        padding: 5px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .calendar-mini-tag.available {
        background: #d1fae5;
        color: #065f46;
    }

    .calendar-mini-tag.partial {
        background: #eff6ff;
        color: #2563eb;
    }

    .calendar-mini-tag.off {
        background: #f1f5f9;
        color: #64748b;
    }

    .calendar-mini-tag.blocked {
        background: #fee2e2;
        color: #b91c1c;
    }

    .bulk-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .bulk-btn {
        border: 1px solid #dbe4ea;
        background: #ffffff;
        color: #334155;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .weekly-list {
        display: grid;
        gap: 12px;
    }

    .day-card {
        border: 1px solid #e7edf3;
        border-radius: 16px;
        background: #ffffff;
        overflow: hidden;
    }

    .day-card-grid {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
    }

    .day-side {
        padding: 16px;
        background: #f8fafc;
        border-right: 1px solid #eef2f7;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .day-name {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
    }

    .day-meta {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }

    .day-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .day-status.available {
        background: #ecfdf5;
        color: #047857;
    }

    .day-status.unavailable {
        background: #f1f5f9;
        color: #64748b;
    }

    .day-main {
        padding: 16px;
        display: grid;
        gap: 14px;
    }

    .day-top-controls {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        align-items: center;
    }

    .toggle-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .toggle-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #0f766e;
    }

    .mini-select-wrap {
        min-width: 180px;
    }

    .mini-label,
    .input-label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .mini-select,
    .input-field {
        width: 100%;
        height: 44px;
        border: 1px solid #dbe4ea;
        border-radius: 12px;
        padding: 0 12px;
        font-size: 14px;
        color: #0f172a;
        background: #ffffff;
        box-sizing: border-box;
    }

    .mini-select:focus,
    .input-field:focus {
        outline: none;
        border-color: #0f766e;
        box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.10);
    }

    .day-input-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .primary-btn {
        min-height: 48px;
        padding: 0 18px;
        border: none;
        border-radius: 14px;
        background: #0f766e;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .summary-box {
        border: 1px solid #e8eef4;
        border-radius: 16px;
        padding: 16px;
        background: #ffffff;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 8px;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .simple-form {
        display: grid;
        gap: 14px;
    }

    .danger-soft-btn {
        min-height: 46px;
        padding: 0 16px;
        border: 1px solid #fecdd3;
        border-radius: 14px;
        background: #fff1f2;
        color: #be123c;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
    }

    .block-list {
        display: grid;
        gap: 12px;
    }

    .block-item {
        border: 1px solid #e8eef4;
        border-radius: 16px;
        padding: 14px;
        background: #ffffff;
    }

    .block-date {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .block-time {
        margin-top: 6px;
        font-size: 13px;
        color: #64748b;
    }

    .block-reason {
        margin: 10px 0 14px;
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    .remove-btn {
        min-height: 40px;
        padding: 0 14px;
        border: none;
        border-radius: 12px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }

    .empty-text {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 1199.98px) {
        .availability-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .day-card-grid {
            grid-template-columns: 1fr;
        }

        .day-side {
            border-right: none;
            border-bottom: 1px solid #eef2f7;
        }

        .day-input-grid,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .calendar-box {
            overflow-x: auto;
        }

        .calendar-weekdays,
        .calendar-grid {
            min-width: 700px;
        }
    }
</style>


@php
    $totalAvailableDays = 0;
    $totalUnavailableDays = 0;

    foreach ($dayLabels as $dayValue => $dayLabel) {
        $schedule = $schedules[$dayValue] ?? null;
        $isAvailable = old("days.$dayValue.is_available", $schedule->is_available ?? false);

        if ($isAvailable) {
            $totalAvailableDays++;
        } else {
            $totalUnavailableDays++;
        }
    }

    $calendarDays = [
        ['date' => 30, 'muted' => true],
        ['date' => 31, 'muted' => true],
        ['date' => 1, 'muted' => false],
        ['date' => 2, 'muted' => false],
        ['date' => 3, 'muted' => false],
        ['date' => 4, 'muted' => false],
        ['date' => 5, 'muted' => false],
        ['date' => 6, 'muted' => false],
        ['date' => 7, 'muted' => false],
        ['date' => 8, 'muted' => false],
        ['date' => 9, 'muted' => false],
        ['date' => 10, 'muted' => false],
        ['date' => 11, 'muted' => false],
        ['date' => 12, 'muted' => false],
        ['date' => 13, 'muted' => false],
        ['date' => 14, 'muted' => false],
        ['date' => 15, 'muted' => false],
        ['date' => 16, 'muted' => false],
        ['date' => 17, 'muted' => false],
        ['date' => 18, 'muted' => false],
        ['date' => 19, 'muted' => false],
        ['date' => 20, 'muted' => false],
        ['date' => 21, 'muted' => false],
        ['date' => 22, 'muted' => false],
        ['date' => 23, 'muted' => false],
        ['date' => 24, 'muted' => false],
        ['date' => 25, 'muted' => false],
        ['date' => 26, 'muted' => false],
        ['date' => 27, 'muted' => false],
        ['date' => 28, 'muted' => false],
        ['date' => 29, 'muted' => false],
        ['date' => 30, 'muted' => false],
        ['date' => 1, 'muted' => true],
        ['date' => 2, 'muted' => true],
        ['date' => 3, 'muted' => true],
    ];

    $calendarWeekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $weekdayOrder = [
        'sunday' => 0,
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
    ];

    $schedulePreview = [];
    foreach ($dayLabels as $dayValue => $dayLabel) {
        $schedule = $schedules[$dayValue] ?? null;
        $isAvailable = old("days.$dayValue.is_available", $schedule->is_available ?? false);

        $schedulePreview[$dayValue] = [
            'label' => $dayLabel,
            'available' => (bool) $isAvailable,
            'start' => old("days.$dayValue.start_time", isset($schedule->start_time) ? substr($schedule->start_time, 0, 5) : ''),
            'end' => old("days.$dayValue.end_time", isset($schedule->end_time) ? substr($schedule->end_time, 0, 5) : ''),
        ];
    }

    $monthTitle = now()->format('F Y');

    $dateOverridesMap = collect(
        isset($dateOverrides) && method_exists($dateOverrides, 'getCollection')
            ? $dateOverrides->getCollection()
            : ($dateOverrides ?? [])
    )->keyBy('override_date');

    $selectedCalendarDate = old('unavailable_date', now()->toDateString());
@endphp











<div class="availability-page">
    <div class="availability-header">
        <div>
            <h2 class="availability-title">Dentist Availability</h2>
            <p class="availability-subtitle">
                Manage your weekly schedule and click a calendar date to mark it available, unavailable, or set a time range.
            </p>
        </div>
    </div>

    <div class="availability-layout">
        <div class="availability-left">
            <div class="availability-panel">
                <div class="calendar-card-top">
                    <div class="calendar-toolbar">
                        <div class="calendar-toolbar-left">
                            <button type="button" class="calendar-btn">Today</button>
                            <button type="button" class="calendar-btn">←</button>
                            <h3 class="calendar-title">{{ $monthTitle }}</h3>
                            <button type="button" class="calendar-btn">→</button>
                        </div>

                        <div class="calendar-toolbar-right">
                            <button type="button" class="calendar-btn active">Month</button>
                        </div>
                    </div>

                    <div class="selected-date-row">
                        <span class="selected-date-label">Selected Date:</span>
                        <span id="selectedCalendarDateLabel" class="selected-date-value">
                            {{ $selectedCalendarDate }}
                        </span>
                    </div>

                    <div id="calendarActionBox" class="calendar-action-box">
                        <div class="calendar-action-top">
                            <div>
                                <div class="calendar-action-caption">Quick Action</div>
                                <div id="calendarActionDateText" class="calendar-action-date">No date selected</div>
                            </div>

                            <button type="button" id="closeCalendarActionBox" class="calendar-btn">
                                Close
                            </button>
                        </div>

                        <div class="calendar-action-buttons">
                            <button type="button" id="actionMarkAvailable" class="calendar-btn">
                                Mark Available
                            </button>

                            <button type="button" id="actionMarkUnavailable" class="calendar-btn">
                                Mark Unavailable
                            </button>

                            <button type="button" id="actionSetTimeRange" class="calendar-btn">
                                Set Time Range
                            </button>
                        </div>
                    </div>

                    <div class="calendar-box">
                        <div class="calendar-weekdays">
                            @foreach($calendarWeekdays as $weekday)
                                <div class="calendar-weekday">{{ $weekday }}</div>
                            @endforeach
                        </div>

                        <div class="calendar-grid" id="availabilityCalendarGrid">
                            @foreach($calendarDays as $index => $cell)
                                @php
                                    $baseDate = now()->startOfMonth()->startOfWeek();
                                    $cellFullDate = $baseDate->copy()->addDays($index)->format('Y-m-d');

                                    $cellWeekdayIndex = $index % 7;
                                    $cellDayKey = array_search($cellWeekdayIndex, $weekdayOrder, true);
                                    $cellPreview = $cellDayKey !== false ? ($schedulePreview[$cellDayKey] ?? null) : null;

                                    $override = $dateOverridesMap->get($cellFullDate);

                                    if ($override) {
                                        $cellStatus = $override->is_available ? 'override-available' : 'override-unavailable';
                                    } elseif (!$cell['muted'] && $cellPreview && $cellPreview['available']) {
                                        $cellStatus = 'weekly-available';
                                    } else {
                                        $cellStatus = '';
                                    }
                                @endphp

                                <div
                                    class="calendar-cell {{ $cell['muted'] ? 'muted' : '' }} {{ $cellStatus }} {{ (!$cell['muted'] && $selectedCalendarDate === $cellFullDate) ? 'selected' : '' }}"
                                    data-date="{{ $cellFullDate }}"
                                    data-selectable="{{ $cell['muted'] ? '0' : '1' }}"
                                >
                                    <div class="calendar-date">{{ $cell['date'] }}</div>

                                    @if(!$cell['muted'])
                                        @if($override)
                                            <div class="calendar-mini-tag {{ $override->is_available ? 'available' : 'blocked' }}">
                                                {{ $override->is_available ? 'Available' : 'Unavailable' }}
                                            </div>
                                        @elseif($cellPreview && $cellPreview['available'] && $cellPreview['start'] && $cellPreview['end'])
                                            <div class="calendar-mini-tag available">
                                                {{ $cellPreview['start'] }} - {{ $cellPreview['end'] }}
                                            </div>
                                        @elseif($cellPreview && $cellPreview['available'])
                                            <div class="calendar-mini-tag partial">
                                                Available
                                            </div>
                                        @else
                                            <div class="calendar-mini-tag off">
                                                Unavailable
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="availability-panel-body">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Weekly Availability</h3>
                            <p class="section-text">
                                Set your working hours for each day.
                            </p>
                        </div>
                    </div>

                    <div class="bulk-actions">
                        <button type="button" class="bulk-btn" id="markAllAvailableBtn">Mark All Available</button>
                        <button type="button" class="bulk-btn" id="markAllUnavailableBtn">Mark All Unavailable</button>
                    </div>

                    <form method="POST" action="{{ route('dentist.availability.store') }}">
                        @csrf

                        <div class="weekly-list">
                            @foreach($dayLabels as $dayValue => $dayLabel)
                                @php $schedule = $schedules[$dayValue] ?? null; @endphp
                                @php
                                    $isAvailable = old("days.$dayValue.is_available", $schedule->is_available ?? false);
                                    $startValue = old("days.$dayValue.start_time", isset($schedule->start_time) ? substr($schedule->start_time,0,5) : '');
                                    $endValue = old("days.$dayValue.end_time", isset($schedule->end_time) ? substr($schedule->start_time ? $schedule->end_time : '',0,5) : '');
                                    $maxPatientsValue = old("days.$dayValue.max_patients", $schedule->max_patients ?? 20);
                                @endphp

                                <div class="day-card availability-day-card" data-day="{{ $dayValue }}">
                                    <div class="day-card-grid">
                                        <div class="day-side">
                                            <h4 class="day-name">{{ $dayLabel }}</h4>

                                            <div class="day-status {{ $isAvailable ? 'available' : 'unavailable' }}" data-status-badge="{{ $dayValue }}">
                                                {{ $isAvailable ? 'Available' : 'Unavailable' }}
                                            </div>

                                            <div class="day-meta">
                                                @if($startValue && $endValue)
                                                    {{ $startValue }} to {{ $endValue }}
                                                @else
                                                    No time set yet
                                                @endif
                                            </div>
                                        </div>

                                        <div class="day-main">
                                            <div class="day-top-controls">
                                                <label class="toggle-label">
                                                    <input
                                                        type="checkbox"
                                                        name="days[{{ $dayValue }}][is_available]"
                                                        value="1"
                                                        class="availability-checkbox"
                                                        data-day="{{ $dayValue }}"
                                                        {{ $isAvailable ? 'checked' : '' }}>
                                                    <span>Available for booking</span>
                                                </label>

                                                <div class="mini-select-wrap">
                                                    <label class="mini-label">Status</label>
                                                    <select class="mini-select availability-status-select" data-day="{{ $dayValue }}">
                                                        <option value="available" {{ $isAvailable ? 'selected' : '' }}>Available</option>
                                                        <option value="unavailable" {{ !$isAvailable ? 'selected' : '' }}>Unavailable</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="day-input-grid">
                                                <div>
                                                    <label class="input-label">Start Time</label>
                                                    <input
                                                        type="time"
                                                        name="days[{{ $dayValue }}][start_time]"
                                                        class="input-field form-control"
                                                        value="{{ $startValue }}">
                                                </div>

                                                <div>
                                                    <label class="input-label">End Time</label>
                                                    <input
                                                        type="time"
                                                        name="days[{{ $dayValue }}][end_time]"
                                                        class="input-field form-control"
                                                        value="{{ $endValue }}">
                                                </div>

                                                <div>
                                                    <label class="input-label">Max Patients</label>
                                                    <input
                                                        type="number"
                                                        name="days[{{ $dayValue }}][max_patients]"
                                                        class="input-field form-control"
                                                        value="{{ $maxPatientsValue }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-top: 18px;">
                            <button class="primary-btn" type="submit">Save Weekly Availability</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="availability-right">
            <div class="availability-panel">
                <div class="availability-panel-body">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Schedule Summary</h3>
                            <p class="section-text">
                                Quick overview of your weekly setup.
                            </p>
                        </div>
                    </div>

                    <div class="summary-grid">
    <div class="summary-box">
        <div class="summary-label">Available Days</div>
        <div class="summary-value">{{ $summary['available_days'] }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-label">Unavailable Days</div>
        <div class="summary-value">{{ $summary['unavailable_days'] }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-label">Date Blocks</div>
        <div class="summary-value">{{ $summary['date_blocks'] }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-label">Weekly Entries</div>
        <div class="summary-value">{{ $summary['weekly_entries'] }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-label">Available Overrides</div>
        <div class="summary-value">{{ $summary['available_overrides'] }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-label">Unavailable Overrides</div>
        <div class="summary-value">{{ $summary['unavailable_overrides'] }}</div>
    </div>
</div>
                </div>
            </div>

            <div class="availability-panel">
                <div class="availability-panel-body">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Block Date / Time</h3>
                            <p class="section-text">
                                Use this when you want to block only a specific time range.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dentist.unavailable-dates.store') }}" class="simple-form">
                        @csrf

                        <div>
                            <label class="input-label">Selected Calendar Date</label>
                            <input
                                type="date"
                                id="selectedCalendarDateInput"
                                name="unavailable_date"
                                class="input-field form-control"
                                value="{{ old('unavailable_date', now()->toDateString()) }}"
                                required>
                        </div>

                        <div>
                            <label class="input-label">Start Time</label>
                            <input type="time" id="blockStartTimeInput" name="start_time" class="input-field form-control">
                        </div>

                        <div>
                            <label class="input-label">End Time</label>
                            <input type="time" id="blockEndTimeInput" name="end_time" class="input-field form-control">
                        </div>

                        <div>
                            <label class="input-label">Reason</label>
                            <input type="text" name="reason" id="blockReasonInput" class="input-field form-control">
                        </div>

                        <div id="calendarFormModeText" style="font-size:12px;font-weight:700;color:#64748b;margin-top:-4px;">
                            Select a date from the calendar or enter the details manually.
                        </div>

                        <button class="danger-soft-btn" type="submit">Add Block</button>
                    </form>
                </div>
            </div>

            <div class="availability-panel">
                <div class="availability-panel-body">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Blocked Dates</h3>
                            <p class="section-text">
                                Remove blocked dates when needed.
                            </p>
                        </div>
                    </div>

                    <div class="block-list">
                        @forelse($unavailableDates as $item)
                            <div class="block-item">
                                <div class="block-date">{{ $item->unavailable_date }}</div>
                                <div class="block-time">{{ $item->start_time }} - {{ $item->end_time }}</div>

                                <div class="block-reason">
                                    {{ $item->reason }}
                                </div>

                                <form method="POST" action="{{ route('dentist.unavailable-dates.destroy', $item->unavailable_id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="remove-btn" type="submit">Remove</button>
                                </form>
                            </div>
                        @empty
                            <p class="empty-text">No blocked dates.</p>
                        @endforelse
                    </div>

                    <div style="margin-top: 18px;">
                        {{ $unavailableDates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="dateOverrideForm" method="POST" action="{{ route('dentist.availability.date-override.store') }}">
    @csrf
    <input type="hidden" name="override_date" id="overrideDateInput">
    <input type="hidden" name="is_available" id="overrideStatusInput">
    <input type="hidden" name="start_time" id="overrideStartTimeInput">
    <input type="hidden" name="end_time" id="overrideEndTimeInput">
    <input type="hidden" name="reason" id="overrideReasonInput">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.availability-status-select');
    const checkboxes = document.querySelectorAll('.availability-checkbox');
    const markAllAvailableBtn = document.getElementById('markAllAvailableBtn');
    const markAllUnavailableBtn = document.getElementById('markAllUnavailableBtn');

    const calendarGrid = document.getElementById('availabilityCalendarGrid');
    const selectedCalendarDateLabel = document.getElementById('selectedCalendarDateLabel');
    const selectedCalendarDateInput = document.getElementById('selectedCalendarDateInput');

    const calendarActionBox = document.getElementById('calendarActionBox');
    const calendarActionDateText = document.getElementById('calendarActionDateText');
    const closeCalendarActionBox = document.getElementById('closeCalendarActionBox');

    const actionMarkAvailable = document.getElementById('actionMarkAvailable');
    const actionMarkUnavailable = document.getElementById('actionMarkUnavailable');
    const actionSetTimeRange = document.getElementById('actionSetTimeRange');

    const blockStartTimeInput = document.getElementById('blockStartTimeInput');
    const blockEndTimeInput = document.getElementById('blockEndTimeInput');
    const blockReasonInput = document.getElementById('blockReasonInput');
    const calendarFormModeText = document.getElementById('calendarFormModeText');

    const overrideForm = document.getElementById('dateOverrideForm');
    const overrideDateInput = document.getElementById('overrideDateInput');
    const overrideStatusInput = document.getElementById('overrideStatusInput');
    const overrideStartTimeInput = document.getElementById('overrideStartTimeInput');
    const overrideEndTimeInput = document.getElementById('overrideEndTimeInput');
    const overrideReasonInput = document.getElementById('overrideReasonInput');

    let activeCalendarDate = null;

    function updateDayState(day, isAvailable) {
        const checkbox = document.querySelector('.availability-checkbox[data-day="' + day + '"]');
        const select = document.querySelector('.availability-status-select[data-day="' + day + '"]');
        const badge = document.querySelector('[data-status-badge="' + day + '"]');
        const card = document.querySelector('.availability-day-card[data-day="' + day + '"]');

        if (checkbox) checkbox.checked = isAvailable;
        if (select) select.value = isAvailable ? 'available' : 'unavailable';

        if (badge) {
            badge.textContent = isAvailable ? 'Available' : 'Unavailable';
            badge.classList.remove('available', 'unavailable');
            badge.classList.add(isAvailable ? 'available' : 'unavailable');
        }

        if (card) {
            card.style.opacity = isAvailable ? '1' : '0.85';
        }
    }

    selects.forEach(function (select) {
        select.addEventListener('change', function () {
            updateDayState(this.dataset.day, this.value === 'available');
        });
    });

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateDayState(this.dataset.day, this.checked);
        });
    });

    if (markAllAvailableBtn) {
        markAllAvailableBtn.addEventListener('click', function () {
            selects.forEach(function (select) {
                updateDayState(select.dataset.day, true);
            });
        });
    }

    if (markAllUnavailableBtn) {
        markAllUnavailableBtn.addEventListener('click', function () {
            selects.forEach(function (select) {
                updateDayState(select.dataset.day, false);
            });
        });
    }

    function showCalendarActionBox(selectedDate) {
        activeCalendarDate = selectedDate;

        if (calendarActionBox) {
            calendarActionBox.style.display = 'block';
        }

        if (calendarActionDateText) {
            calendarActionDateText.textContent = selectedDate;
        }

        if (selectedCalendarDateLabel) {
            selectedCalendarDateLabel.textContent = selectedDate;
        }

        if (selectedCalendarDateInput) {
            selectedCalendarDateInput.value = selectedDate;
        }
    }

    if (closeCalendarActionBox) {
        closeCalendarActionBox.addEventListener('click', function () {
            if (calendarActionBox) {
                calendarActionBox.style.display = 'none';
            }
        });
    }

    if (calendarGrid) {
        calendarGrid.addEventListener('click', function (event) {
            const cell = event.target.closest('.calendar-cell');

            if (!cell || cell.dataset.selectable !== '1') {
                return;
            }

            document.querySelectorAll('.calendar-cell').forEach(function (item) {
                item.classList.remove('selected');
            });

            cell.classList.add('selected');
            showCalendarActionBox(cell.dataset.date);
        });
    }

    if (actionMarkAvailable) {
        actionMarkAvailable.addEventListener('click', function () {
            if (!activeCalendarDate || !overrideForm) return;

            overrideDateInput.value = activeCalendarDate;
            overrideStatusInput.value = '1';
            overrideStartTimeInput.value = '';
            overrideEndTimeInput.value = '';
            overrideReasonInput.value = 'Available override';

            overrideForm.submit();
        });
    }

    if (actionMarkUnavailable) {
        actionMarkUnavailable.addEventListener('click', function () {
            if (!activeCalendarDate || !overrideForm) return;

            overrideDateInput.value = activeCalendarDate;
            overrideStatusInput.value = '0';
            overrideStartTimeInput.value = '';
            overrideEndTimeInput.value = '';
            overrideReasonInput.value = 'Unavailable override';

            overrideForm.submit();
        });
    }

    if (actionSetTimeRange) {
        actionSetTimeRange.addEventListener('click', function () {
            if (!activeCalendarDate) return;

            if (selectedCalendarDateInput) {
                selectedCalendarDateInput.value = activeCalendarDate;
            }

            if (selectedCalendarDateLabel) {
                selectedCalendarDateLabel.textContent = activeCalendarDate;
            }

            if (blockReasonInput) {
                blockReasonInput.value = 'Partial unavailable time range';
            }

            if (calendarFormModeText) {
                calendarFormModeText.textContent = 'Selected mode: set a blocked time range for the chosen date.';
            }

            if (blockStartTimeInput) {
                blockStartTimeInput.focus();
            }
        });
    }
});
</script>
@endsection
