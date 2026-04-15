@extends('staff.layouts.app')

@section('content')
<style>
    .schedule-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .schedule-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .schedule-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .schedule-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .page-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 18px;
    }

    .card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
    }

    .card h2 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    .card-note {
        margin: 0 0 16px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
    }

    .weekly-list {
        display: grid;
        gap: 12px;
    }

    .weekly-row {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr 1fr 1fr;
        gap: 10px;
        align-items: center;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px;
    }

    .weekly-day {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .badge-open {
        background: #ecfdf3;
        color: #15803d;
    }

    .badge-closed {
        background: #fef2f2;
        color: #dc2626;
    }

    .time-box {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .blocks-list {
        display: grid;
        gap: 12px;
    }

    .block-item {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px;
    }

    .block-date {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .block-meta {
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    .today-box {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 14px;
        padding: 16px;
    }

    .today-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .today-text {
        font-size: 13px;
        color: #475569;
        line-height: 1.7;
    }

    .empty-state {
        font-size: 14px;
        color: #64748b;
        padding: 14px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    @media (max-width: 1100px) {
        .schedule-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .page-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .weekly-row {
            grid-template-columns: 1fr;
        }

        .schedule-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $openDays = $weeklySchedules->where('is_open', true)->count();
    $closedDays = $weeklySchedules->where('is_open', false)->count();
    $todayName = strtolower(now()->format('l'));
    $todaySchedule = $weeklySchedules->firstWhere('day_of_week', $todayName);
    $todayStatus = $todaySchedule && $todaySchedule->is_open ? 'Open' : 'Closed';
@endphp

<div class="schedule-page">
    <div class="schedule-header">
        <h1>Clinic Schedule</h1>
        <p>View the clinic weekly schedule and blocked dates. Staff can monitor schedule information here, while clinic schedule changes remain managed in the backend.</p>
    </div>

    <div class="schedule-stats">
        <div class="stat-card">
            <div class="stat-label">Open Days</div>
            <div class="stat-value">{{ $openDays }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Closed Days</div>
            <div class="stat-value">{{ $closedDays }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Date Blocks</div>
            <div class="stat-value">{{ $blocks->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Today</div>
            <div class="stat-value" style="font-size:22px;">{{ $todayStatus }}</div>
        </div>
    </div>

    <div class="page-grid">
        <div class="card">
            <h2>Weekly Clinic Schedule</h2>
            <p class="card-note">This section reads the clinic weekly schedule directly from the database.</p>

            <div class="weekly-list">
                @forelse($weeklySchedules as $schedule)
                    <div class="weekly-row">
                        <div class="weekly-day">
                            {{ ucfirst($schedule->day_of_week) }}
                        </div>

                        <div>
                            @if($schedule->is_open)
                                <span class="badge badge-open">Open</span>
                            @else
                                <span class="badge badge-closed">Closed</span>
                            @endif
                        </div>

                        <div class="time-box">
                            {{ $schedule->open_time ? \Carbon\Carbon::parse($schedule->open_time)->format('h:i A') : '—' }}
                        </div>

                        <div class="time-box">
                            {{ $schedule->close_time ? \Carbon\Carbon::parse($schedule->close_time)->format('h:i A') : '—' }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No weekly clinic schedule found.</div>
                @endforelse
            </div>
        </div>

        <div style="display:grid; gap:18px;">
            <div class="card">
                <h2>Today’s Clinic Status</h2>
                <p class="card-note">Quick daily reference for staff.</p>

                <div class="today-box">
                    <div class="today-title">{{ ucfirst($todayName) }}</div>
                    <div class="today-text">
                        <strong>Status:</strong> {{ $todayStatus }}<br>

                        @if($todaySchedule && $todaySchedule->is_open)
                            <strong>Open Time:</strong>
                            {{ $todaySchedule->open_time ? \Carbon\Carbon::parse($todaySchedule->open_time)->format('h:i A') : '—' }}<br>
                            <strong>Close Time:</strong>
                            {{ $todaySchedule->close_time ? \Carbon\Carbon::parse($todaySchedule->close_time)->format('h:i A') : '—' }}
                        @else
                            Clinic is closed today.
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <h2>Blocked Dates and Time Ranges</h2>
        <p class="card-note">This section reads blocked clinic dates and time ranges directly from the database.</p>

        <div class="blocks-list">
            @forelse($blocks as $block)
                <div class="block-item">
                    <div class="block-date">
                        {{ \Carbon\Carbon::parse($block->block_date)->format('M d, Y') }}
                    </div>

                    <div class="block-meta">
                        <strong>Type:</strong>
                        {{ !empty($block->is_full_day) ? 'Full Day' : 'Partial Time Block' }}
                        <br>

                        <strong>Time:</strong>
                        @if(!empty($block->is_full_day))
                            Whole day
                        @else
                            {{ $block->start_time ? \Carbon\Carbon::parse($block->start_time)->format('h:i A') : '—' }}
                            -
                            {{ $block->end_time ? \Carbon\Carbon::parse($block->end_time)->format('h:i A') : '—' }}
                        @endif
                        <br>

                        <strong>Reason:</strong>
                        {{ $block->reason ?: 'No reason provided' }}
                    </div>
                </div>
            @empty
                <div class="empty-state">No clinic blocks found.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
