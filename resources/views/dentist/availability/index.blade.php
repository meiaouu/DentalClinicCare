@extends('dentist.layouts.app')

@section('page_title', 'Availability')

@section('dentist_content')
<style>
    .availability-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .availability-header h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }

    .availability-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .availability-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
        align-items: start;
    }

    .availability-left,
    .availability-right {
        display: grid;
        gap: 16px;
    }

    .availability-box {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 16px;
    }

    .section-title {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .section-text {
        margin: 0 0 14px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }

    .calendar-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
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

    .calendar-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-simple,
    .btn-primary,
    .btn-danger,
    .btn-remove {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #334155;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-simple.active {
        background: #14b8a6;
        color: #ffffff;
        border-color: #14b8a6;
    }

    .btn-primary {
        background: #0f766e;
        color: #ffffff;
        border-color: #0f766e;
    }

    .btn-danger {
        background: #fff1f2;
        color: #be123c;
        border-color: #fecdd3;
    }

    .btn-remove {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .selected-date-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .selected-date-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .selected-date-value {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #0f766e;
        font-size: 13px;
        font-weight: 700;
    }

    .calendar-action-box {
        display: none;
        margin-bottom: 12px;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }

    .calendar-action-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .calendar-action-caption {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .calendar-action-date {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 4px;
    }

    .calendar-action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .calendar-box {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
    }

    .calendar-weekdays,
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .calendar-weekday {
        padding: 10px 6px;
        text-align: center;
        background: #f3f4f6;
        border-bottom: 1px solid #e5e7eb;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .calendar-cell {
        min-height: 82px;
        padding: 8px;
        border-right: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
        cursor: pointer;
    }

    .calendar-cell:nth-child(7n) {
        border-right: none;
    }

    .calendar-cell:hover {
        background: #f9fafb;
    }

    .calendar-cell.muted,
    .calendar-cell.muted:hover {
        background: #fafafa;
        cursor: not-allowed;
    }

    .calendar-cell.weekly-available {
        background: #f0fdf4;
    }

    .calendar-cell.override-available {
        background: #dcfce7;
    }

    .calendar-cell.override-unavailable {
        background: #fee2e2;
    }

    .calendar-cell.blocked {
        background: #fff7ed;
    }

    .calendar-cell.selected {
        outline: 2px solid #0f766e;
        outline-offset: -2px;
    }

    .calendar-date {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .calendar-cell.muted .calendar-date {
        color: #cbd5e1;
    }

    .calendar-mini-tag {
        display: inline-block;
        padding: 4px 6px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .calendar-mini-tag.available {
        background: #d1fae5;
        color: #065f46;
    }

    .calendar-mini-tag.partial {
        background: #dbeafe;
        color: #1d4ed8;
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
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .weekly-list {
        display: grid;
        gap: 10px;
    }

    .day-card {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        overflow: hidden;
    }

    .accordion-day-header {
        width: 100%;
        border: none;
        background: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        cursor: pointer;
        text-align: left;
    }

    .accordion-day-header-left {
        display: grid;
        gap: 4px;
    }

    .accordion-day-header-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .accordion-day-arrow {
        font-size: 14px;
        color: #64748b;
    }

    .accordion-day-card.is-open .accordion-day-arrow {
        transform: rotate(180deg);
    }

    .accordion-day-content {
        border-top: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .day-name {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .day-meta {
        font-size: 12px;
        color: #64748b;
    }

    .day-status {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    .day-status.available {
        background: #dcfce7;
        color: #166534;
    }

    .day-status.unavailable {
        background: #f1f5f9;
        color: #64748b;
    }

    .day-main {
        padding: 14px;
        display: grid;
        gap: 14px;
    }

    .day-top-controls {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .toggle-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .toggle-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #0f766e;
    }

    .mini-select-wrap {
        min-width: 180px;
    }

    .mini-label,
    .input-label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }

    .mini-select,
    .input-field {
        width: 100%;
        height: 40px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 10px;
        font-size: 14px;
        color: #0f172a;
        background: #ffffff;
        box-sizing: border-box;
    }

    .day-input-grid,
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .summary-box {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 14px;
        background: #ffffff;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
    }

    .simple-form,
    .block-list {
        display: grid;
        gap: 12px;
    }

    .block-item {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 12px;
        background: #ffffff;
    }

    .block-date {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .block-time {
        margin-top: 5px;
        font-size: 13px;
        color: #64748b;
    }

    .block-reason {
        margin: 8px 0 12px;
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
    }

    .empty-text {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 1199px) {
        .availability-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
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
    $calendarBaseDate = $calendarMonthStart->copy();
    $calendarGridStart = $calendarBaseDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);

    $calendarDays = collect(range(0, 41))->map(function ($offset) use ($calendarGridStart, $calendarBaseDate) {
        $date = $calendarGridStart->copy()->addDays($offset);

        return [
            'date' => $date->day,
            'full_date' => $date->format('Y-m-d'),
            'muted' => $date->month !== $calendarBaseDate->month,
            'weekday_index' => (int) $date->dayOfWeek,
        ];
    })->all();

    $calendarWeekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $selectedCalendarDate = old('unavailable_date', now()->toDateString());

    $previousMonth = $calendarMonthStart->copy()->subMonth()->format('Y-m');
    $nextMonth = $calendarMonthStart->copy()->addMonth()->format('Y-m');
@endphp

<div class="availability-page">
    <div class="availability-header">
        <h2>Dentist Availability</h2>
        <p>Set your weekly schedule and click a calendar date to make changes.</p>
    </div>

    <div class="availability-layout">
        <div class="availability-left">
            <div class="availability-box">
                <div class="calendar-toolbar">
                    <div class="calendar-toolbar-left">
                        <a href="{{ route('dentist.availability.index', ['month' => now()->format('Y-m')]) }}" class="btn-simple">Today</a>
                        <a href="{{ route('dentist.availability.index', ['month' => $previousMonth]) }}" class="btn-simple">←</a>
                        <h3 class="calendar-title">{{ $summary['month'] }}</h3>
                        <a href="{{ route('dentist.availability.index', ['month' => $nextMonth]) }}" class="btn-simple">→</a>
                    </div>

                    <div class="calendar-toolbar-right">
                        <button type="button" class="btn-simple active">Month</button>
                    </div>
                </div>

                <div class="selected-date-row">
                    <span class="selected-date-label">Selected Date:</span>
                    <span id="selectedCalendarDateLabel" class="selected-date-value">{{ $selectedCalendarDate }}</span>
                </div>

                <div id="calendarActionBox" class="calendar-action-box">
                    <div class="calendar-action-top">
                        <div>
                            <div class="calendar-action-caption">Quick Action</div>
                            <div id="calendarActionDateText" class="calendar-action-date">No date selected</div>
                        </div>

                        <button type="button" id="closeCalendarActionBox" class="btn-simple">Close</button>
                    </div>

                    <div class="calendar-action-buttons">
                        <button type="button" id="actionFullDayAvailable" class="btn-simple">Available</button>
                        <button type="button" id="actionMorningAvailable" class="btn-simple">Morning</button>
                        <button type="button" id="actionAfternoonAvailable" class="btn-simple">Afternoon</button>
                        <button type="button" id="actionMarkUnavailable" class="btn-simple">Unavailable</button>
                        <button type="button" id="actionSetTimeRange" class="btn-simple">Custom Time</button>
                    </div>
                </div>

                <div class="calendar-box">
                    <div class="calendar-weekdays">
                        @foreach($calendarWeekdays as $weekday)
                            <div class="calendar-weekday">{{ $weekday }}</div>
                        @endforeach
                    </div>

                    <div class="calendar-grid" id="availabilityCalendarGrid">
                        @foreach($calendarDays as $cell)
                            @php
                                $cellDate = $cell['full_date'];
                                $override = $monthlyDateOverrides->get($cellDate);
                                $blockedDate = $monthlyUnavailableDates->get($cellDate);
                                $weeklySchedule = $schedules[$cell['weekday_index']] ?? null;

                                $cellStatus = '';
                                $cellTag = null;
                                $cellTagClass = null;

                                if ($cell['muted']) {
                                    $cellStatus = '';
                                } elseif ($override) {
                                    $cellStatus = $override->is_available ? 'override-available' : 'override-unavailable';
                                    $cellTag = $override->is_available ? 'Available' : 'Unavailable';
                                    $cellTagClass = $override->is_available ? 'available' : 'blocked';
                                } elseif ($blockedDate) {
                                    $isPartial = !empty($blockedDate->start_time) && !empty($blockedDate->end_time);
                                    $cellStatus = 'blocked';
                                    $cellTag = $isPartial ? 'Partial Block' : 'Blocked';
                                    $cellTagClass = $isPartial ? 'partial' : 'blocked';
                                } elseif ($weeklySchedule && (int) $weeklySchedule->is_available === 1) {
                                    $cellStatus = 'weekly-available';
                                    $cellTag = 'Available';
                                    $cellTagClass = 'available';
                                } else {
                                    $cellStatus = 'weekly-unavailable';
                                    $cellTag = 'Unavailable';
                                    $cellTagClass = 'off';
                                }
                            @endphp

                            <div
                                class="calendar-cell {{ $cell['muted'] ? 'muted' : '' }} {{ $cellStatus }} {{ (!$cell['muted'] && $selectedCalendarDate === $cellDate) ? 'selected' : '' }}"
                                data-date="{{ $cellDate }}"
                                data-selectable="{{ $cell['muted'] ? '0' : '1' }}"
                            >
                                <div class="calendar-date">{{ $cell['date'] }}</div>

                                @if(!$cell['muted'] && $cellTag)
                                    <div class="calendar-mini-tag {{ $cellTagClass }}">{{ $cellTag }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="availability-box">
                <h3 class="section-title">Weekly Availability</h3>
                <p class="section-text">Click a day to open and edit its schedule.</p>

                <div class="bulk-actions">
                    <button type="button" class="btn-simple" id="markAllAvailableBtn">Mark All Available</button>
                    <button type="button" class="btn-simple" id="markAllUnavailableBtn">Mark All Unavailable</button>
                </div>

                <form method="POST" action="{{ route('dentist.availability.store') }}">
                    @csrf

                    <div class="weekly-list">
                        @foreach($dayLabels as $dayValue => $dayLabel)
                            @php
                                $schedule = $schedules[$dayValue] ?? null;
                                $isAvailable = old("days.$dayValue.is_available", $schedule->is_available ?? false);
                                $startValue = old("days.$dayValue.start_time", isset($schedule->start_time) ? substr($schedule->start_time, 0, 5) : '');
                                $endValue = old("days.$dayValue.end_time", isset($schedule->end_time) ? substr($schedule->end_time, 0, 5) : '');
                                $maxPatientsValue = old("days.$dayValue.max_patients", $schedule->max_patients ?? 20);
                                $isOpen = $loop->first;
                            @endphp

                            <div class="day-card availability-day-card accordion-day-card {{ $isOpen ? 'is-open' : '' }}" data-day="{{ $dayValue }}">
                                <button type="button" class="accordion-day-header" data-day-toggle="{{ $dayValue }}">
                                    <div class="accordion-day-header-left">
                                        <div class="day-name">{{ $dayLabel }}</div>
                                        <div class="day-meta">
                                            @if($startValue && $endValue)
                                                {{ $startValue }} to {{ $endValue }}
                                            @else
                                                No time set yet
                                            @endif
                                        </div>
                                    </div>

                                    <div class="accordion-day-header-right">
                                        <div class="day-status {{ $isAvailable ? 'available' : 'unavailable' }}" data-status-badge="{{ $dayValue }}">
                                            {{ $isAvailable ? 'Available' : 'Unavailable' }}
                                        </div>
                                        <span class="accordion-day-arrow">▾</span>
                                    </div>
                                </button>

                                <div class="accordion-day-content" @if(!$isOpen) style="display:none;" @endif>
                                    <div class="day-main">
                                        <div class="day-top-controls">
                                            <label class="toggle-label">
                                                <input
                                                    type="checkbox"
                                                    name="days[{{ $dayValue }}][is_available]"
                                                    value="1"
                                                    class="availability-checkbox"
                                                    data-day="{{ $dayValue }}"
                                                    {{ $isAvailable ? 'checked' : '' }}
                                                >
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
                                                <input type="time" name="days[{{ $dayValue }}][start_time]" class="input-field" value="{{ $startValue }}">
                                            </div>

                                            <div>
                                                <label class="input-label">End Time</label>
                                                <input type="time" name="days[{{ $dayValue }}][end_time]" class="input-field" value="{{ $endValue }}">
                                            </div>

                                            <div>
                                                <label class="input-label">Max Patients</label>
                                                <input type="number" name="days[{{ $dayValue }}][max_patients]" class="input-field" value="{{ $maxPatientsValue }}" min="1" max="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top:16px;">
                        <button class="btn-primary" type="submit">Save Weekly Availability</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="availability-right">
            <div class="availability-box">
                <h3 class="section-title">Schedule Summary</h3>
                <p class="section-text">Quick overview of this month.</p>

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

            <div class="availability-box">
                <h3 class="section-title">Block Date / Time</h3>
                <p class="section-text">Use this if you want to block a specific date or time.</p>

                <form method="POST" action="{{ route('dentist.unavailable-dates.store') }}" class="simple-form">
                    @csrf

                    <div>
                        <label class="input-label">Selected Calendar Date</label>
                        <input
                            type="date"
                            id="selectedCalendarDateInput"
                            name="unavailable_date"
                            class="input-field"
                            value="{{ old('unavailable_date', now()->toDateString()) }}"
                            required
                        >
                    </div>

                    <div>
                        <label class="input-label">Start Time</label>
                        <input type="time" id="blockStartTimeInput" name="start_time" class="input-field">
                    </div>

                    <div>
                        <label class="input-label">End Time</label>
                        <input type="time" id="blockEndTimeInput" name="end_time" class="input-field">
                    </div>

                    <div>
                        <label class="input-label">Reason</label>
                        <input type="text" name="reason" id="blockReasonInput" class="input-field">
                    </div>

                    <div id="calendarFormModeText" style="font-size:12px;font-weight:600;color:#64748b;">
                        Select a date from the calendar or enter details manually.
                    </div>

                    <button class="btn-danger" type="submit">Add Block</button>
                </form>
            </div>

            <div class="availability-box">
                <h3 class="section-title">Blocked Dates</h3>
                <p class="section-text">You can remove blocked dates here.</p>

                <div class="block-list">
                    @forelse($unavailableDates as $item)
                        <div class="block-item">
                            <div class="block-date">{{ \Carbon\Carbon::parse($item->unavailable_date)->toDateString() }}</div>
                            <div class="block-time">
                                {{ $item->start_time ?: 'Whole day' }}{{ $item->end_time ? ' - ' . $item->end_time : '' }}
                            </div>
                            <div class="block-reason">{{ $item->reason }}</div>

                            <form method="POST" action="{{ route('dentist.unavailable-dates.destroy', $item->unavailable_id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-remove" type="submit">Remove</button>
                            </form>
                        </div>
                    @empty
                        <p class="empty-text">No blocked dates.</p>
                    @endforelse
                </div>

                <div style="margin-top:16px;">
                    {{ $unavailableDates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<form id="dateOverrideForm" method="POST" action="{{ route('dentist.availability.date-override.store') }}">
    @csrf
    <input type="hidden" name="override_date" id="overrideDateInput">
    <input type="hidden" name="is_available" id="overrideStatusInput">
    <input type="hidden" name="availability_mode" id="overrideModeInput">
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

    const actionFullDayAvailable = document.getElementById('actionFullDayAvailable');
    const actionMorningAvailable = document.getElementById('actionMorningAvailable');
    const actionAfternoonAvailable = document.getElementById('actionAfternoonAvailable');
    const actionMarkUnavailable = document.getElementById('actionMarkUnavailable');
    const actionSetTimeRange = document.getElementById('actionSetTimeRange');
    const overrideModeInput = document.getElementById('overrideModeInput');

    const blockStartTimeInput = document.getElementById('blockStartTimeInput');
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

        if (checkbox) checkbox.checked = isAvailable;
        if (select) select.value = isAvailable ? 'available' : 'unavailable';

        if (badge) {
            badge.textContent = isAvailable ? 'Available' : 'Unavailable';
            badge.classList.remove('available', 'unavailable');
            badge.classList.add(isAvailable ? 'available' : 'unavailable');
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

    function submitAvailableOverride(mode, reasonText) {
        if (!activeCalendarDate || !overrideForm) return;

        overrideDateInput.value = activeCalendarDate;
        overrideStatusInput.value = '1';
        overrideModeInput.value = mode;
        overrideStartTimeInput.value = '';
        overrideEndTimeInput.value = '';
        overrideReasonInput.value = reasonText;

        overrideForm.submit();
    }

    if (actionFullDayAvailable) {
        actionFullDayAvailable.addEventListener('click', function () {
            submitAvailableOverride('full_day', 'Available override');
        });
    }

    if (actionMorningAvailable) {
        actionMorningAvailable.addEventListener('click', function () {
            submitAvailableOverride('morning', 'Half day - morning');
        });
    }

    if (actionAfternoonAvailable) {
        actionAfternoonAvailable.addEventListener('click', function () {
            submitAvailableOverride('afternoon', 'Half day - afternoon');
        });
    }

    if (actionMarkUnavailable) {
        actionMarkUnavailable.addEventListener('click', function () {
            if (!activeCalendarDate || !overrideForm) return;

            overrideDateInput.value = activeCalendarDate;
            overrideStatusInput.value = '0';
            overrideModeInput.value = '';
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

            if (calendarFormModeText) {
                calendarFormModeText.textContent = 'Selected mode: set a custom time range for the chosen date.';
            }

            if (blockReasonInput && !blockReasonInput.value) {
                blockReasonInput.value = 'Custom time override';
            }

            if (blockStartTimeInput) {
                blockStartTimeInput.focus();
            }
        });
    }

    document.querySelectorAll('[data-day-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const card = this.closest('.availability-day-card');
            const content = card ? card.querySelector('.accordion-day-content') : null;

            if (!card || !content) return;

            document.querySelectorAll('.availability-day-card').forEach(function (item) {
                if (item !== card) {
                    item.classList.remove('is-open');
                    const itemContent = item.querySelector('.accordion-day-content');
                    if (itemContent) itemContent.style.display = 'none';
                }
            });

            const isOpen = card.classList.contains('is-open');

            if (isOpen) {
                card.classList.remove('is-open');
                content.style.display = 'none';
            } else {
                card.classList.add('is-open');
                content.style.display = '';
            }
        });
    });
});
</script>
@endsection
