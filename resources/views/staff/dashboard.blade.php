@extends('staff.layouts.app')

@section('content')
<style>
    .staff-dashboard {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .staff-ui-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .staff-ui-title-wrap h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
    }

    .staff-ui-title-wrap p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .staff-ui-tools {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .staff-ui-search {
        min-width: 250px;
        height: 44px;
        padding: 0 16px;
        border: 1px solid #dbe2ea;
        border-radius: 999px;
        background: #ffffff;
        color: #334155;
        outline: none;
    }

    .staff-ui-chip {
        display: inline-flex;
        align-items: center;
        height: 42px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #dbe2ea;
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
    }

    .staff-ui-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        font-weight: 800;
        font-size: 14px;
        text-decoration: none;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
    }

    .staff-ui-stats {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .staff-ui-stat {
        background: #ffffff;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .staff-ui-stat-label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .staff-ui-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .staff-ui-card {
        background: #ffffff;
        border: 1px solid #e9eef5;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .staff-ui-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        padding: 22px 22px 14px;
        border-bottom: 1px solid #edf2f7;
    }

    .staff-ui-card-head h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .staff-ui-card-head p {
        margin: 6px 0 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .staff-ui-table-wrap {
        overflow-x: auto;
        padding: 0 14px 14px;
    }

    .staff-ui-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .staff-ui-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid #edf2f7;
        background: #fcfdff;
    }

    .staff-ui-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
        font-size: 14px;
    }

    .staff-ui-table tr:hover {
        background: #fafcff;
    }

    .staff-ui-patient {
        font-weight: 800;
        color: #0f172a;
    }

    .staff-ui-subtext {
        margin-top: 4px;
        font-size: 12px;
        color: #64748b;
    }

    .staff-ui-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 108px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .status-confirmed,
    .status-completed,
    .status-checked_in,
    .status-in_progress {
        background: #ecfdf3;
        color: #15803d;
    }

    .status-pending,
    .status-under_review {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-no_show,
    .status-cancelled,
    .status-rejected {
        background: #fef2f2;
        color: #dc2626;
    }

    .staff-ui-row-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .staff-ui-row-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 12px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .staff-ui-empty {
        padding: 40px 22px;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 1200px) {
        .staff-ui-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .staff-ui-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .staff-ui-search {
            min-width: 100%;
        }
    }

    @media (max-width: 520px) {
        .staff-ui-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="staff-dashboard">
    <div class="staff-ui-topbar">
        <div class="staff-ui-title-wrap">
            <h1>Appointments Overview</h1>
            <p>Manage appointment requests, clinic-day flow, and staff actions from one dashboard.</p>
        </div>

        <div class="staff-ui-tools">
            <input type="text" class="staff-ui-search" placeholder="Search patient, service, or date" readonly>
            <span class="staff-ui-chip">{{ now()->format('M d, Y') }}</span>

            @if(Route::has('staff.appointment-requests.index'))
                <a href="{{ route('staff.appointment-requests.index') }}" class="staff-ui-action">
                    Open Requests
                </a>
            @endif
        </div>
    </div>

    <section class="staff-ui-stats">
        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">Pending Requests</div>
            <div class="staff-ui-stat-value">{{ $stats['pending_requests'] ?? 0 }}</div>
        </div>

        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">Today's Appointments</div>
            <div class="staff-ui-stat-value">{{ $stats['today_appointments'] ?? 0 }}</div>
        </div>

        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">Confirmed</div>
            <div class="staff-ui-stat-value">{{ $stats['confirmed_today'] ?? 0 }}</div>
        </div>

        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">Checked In</div>
            <div class="staff-ui-stat-value">{{ $stats['checked_in_today'] ?? 0 }}</div>
        </div>

        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">Completed</div>
            <div class="staff-ui-stat-value">{{ $stats['completed_today'] ?? 0 }}</div>
        </div>

        <div class="staff-ui-stat">
            <div class="staff-ui-stat-label">No Show</div>
            <div class="staff-ui-stat-value">{{ $stats['no_show_today'] ?? 0 }}</div>
        </div>
    </section>

    <section class="staff-ui-card">
        <div class="staff-ui-card-head">
            <div>
                <h2>Upcoming Appointments</h2>
                <p>Clean appointment list for staff review, confirmation follow-up, and clinic-day monitoring.</p>
            </div>

            @if(Route::has('staff.appointments.index'))
                <a href="{{ route('staff.appointments.index') }}" class="staff-ui-row-link">
                    View Full Schedule
                </a>
            @endif
        </div>

        <div class="staff-ui-table-wrap">
            @if(($appointments ?? collect())->count())
                <table class="staff-ui-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Dentist</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            @php
                                $statusClass = 'status-' . strtolower($appointment->status ?? 'pending');
                                $patientName = trim(($appointment->patient?->first_name ?? 'Guest') . ' ' . ($appointment->patient?->last_name ?? ''));
                            @endphp
                            <tr>
                                <td>
                                    <div class="staff-ui-patient">{{ $patientName ?: 'Guest Patient' }}</div>
                                    <div class="staff-ui-subtext">{{ $appointment->appointment_code ?? '—' }}</div>
                                </td>
                                <td>{{ $appointment->service?->service_name ?? '—' }}</td>
                                <td>{{ optional($appointment->appointment_date)->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
                                    -
                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                </td>
                                <td>
                                    {{ trim(($appointment->dentist?->user?->first_name ?? 'Dentist') . ' ' . ($appointment->dentist?->user?->last_name ?? '')) }}
                                </td>
                                <td>
                                    <span class="staff-ui-badge {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $appointment->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="staff-ui-row-actions">
                                        @if(Route::has('staff.appointments.show'))
                                            <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="staff-ui-row-link">
                                                View
                                            </a>
                                        @endif

                                        @if(Route::has('staff.appointment-requests.show') && $appointment->request_id)
                                            <a href="{{ route('staff.appointment-requests.show', $appointment->request_id) }}" class="staff-ui-row-link">
                                                Request
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="staff-ui-empty">
                    No appointment records found yet. Once requests are confirmed, they will appear here as real appointment records.
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
