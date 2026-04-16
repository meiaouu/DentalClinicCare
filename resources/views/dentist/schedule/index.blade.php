@extends('dentist.layouts.app')

@section('page_title', 'My Schedule')

@section('content')
<style>
    .schedule-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .schedule-header {
        background: #ffffff;
        border: 1px solid #d1d5db;
        padding: 16px;
        border-radius: 10px;
    }

    .schedule-header h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: bold;
        color: #0f172a;
    }

    .schedule-header p {
        margin: 0;
        font-size: 14px;
        color: #475569;
    }

    .schedule-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .summary-box {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 14px;
    }

    .summary-box small {
        display: block;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .summary-box strong {
        font-size: 24px;
        color: #0f172a;
    }

    .schedule-group {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 16px;
    }

    .schedule-group.today {
        border-left: 5px solid #0f9d8a;
    }

    .schedule-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .schedule-group-title {
        font-size: 20px;
        font-weight: bold;
        color: #0f172a;
    }

    .today-label {
        font-size: 12px;
        font-weight: bold;
        background: #d1fae5;
        color: #065f46;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .schedule-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        background: #f9fafb;
    }

    .schedule-item:last-child {
        margin-bottom: 0;
    }

    .schedule-item.past {
        background: #f3f4f6;
        color: #6b7280;
    }

    .schedule-time {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 6px;
        color: #111827;
    }

    .schedule-item.past .schedule-time {
        color: #6b7280;
    }

    .schedule-patient {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #0f172a;
    }

    .schedule-item.past .schedule-patient {
        color: #6b7280;
    }

    .schedule-info {
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    .status-text {
        display: inline-block;
        margin-top: 8px;
        font-size: 12px;
        font-weight: bold;
        color: #0f766e;
    }

    .schedule-item.past .status-text {
        color: #6b7280;
    }

    .patient-type {
        display: inline-block;
        margin-top: 6px;
        font-size: 12px;
        color: #92400e;
    }

    .empty-state {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .schedule-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="schedule-page">
    <div class="schedule-header">
        <h2>My Schedule</h2>

    </div>

    <div class="schedule-summary">
        <div class="summary-box">
            <small>Today</small>
            <strong>{{ $todayCount ?? 0 }}</strong>
        </div>

        <div class="summary-box">
            <small>Upcoming</small>
            <strong>{{ $upcomingCount ?? 0 }}</strong>
        </div>

        <div class="summary-box">
            <small>Completed</small>
            <strong>{{ $completedCount ?? 0 }}</strong>
        </div>
    </div>

    @if(empty($groupedAppointments) || $groupedAppointments->isEmpty())
        <div class="empty-state">
            No scheduled appointments.
        </div>
    @else
        @foreach($groupedAppointments as $group)
            <div class="schedule-group {{ !empty($group['is_today']) ? 'today' : '' }}">
                <div class="schedule-group-header">
                    <div class="schedule-group-title">{{ $group['label'] }}</div>

                    @if(!empty($group['is_today']))
                        <div class="today-label">Today</div>
                    @endif
                </div>

                @foreach($group['items'] as $row)
                    <div class="schedule-item {{ !empty($row['is_past']) ? 'past' : '' }}">
                        <div class="schedule-time">
                            {{ $row['start']->format('h:i A') }} - {{ $row['end']->format('h:i A') }}
                        </div>

                        <div class="schedule-patient">
                            {{ $row['patient_name'] }}
                        </div>

                        <div class="schedule-info">
                            <div><strong>Service:</strong> {{ $row['appointment']->service?->service_name ?? '—' }}</div>
                            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($row['appointment']->appointment_date)->format('M d, Y') }}</div>

                            @if(!empty($row['appointment']->remarks))
                                <div><strong>Notes:</strong> {{ $row['appointment']->remarks }}</div>
                            @endif
                        </div>

                        <div class="status-text">
                            Status: {{ ucfirst(str_replace('_', ' ', $row['appointment']->status)) }}
                        </div>

                        <div class="patient-type">
                            {{ !empty($row['is_returning']) ? 'Returning Patient' : 'New Patient' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection
