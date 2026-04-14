@extends('staff.layouts.app')

@section('content')
<style>
    .dashboard-page {
        display: grid;
        gap: 18px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dashboard-title h1 {
        margin: 0 0 6px;
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
    }

    .dashboard-title p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .dashboard-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #dbe4ea;
        background: #ffffff;
        color: #334155;
    }

    .action-btn.primary {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 18px;
        align-items: start;
    }

    .side-grid {
        display: grid;
        gap: 18px;
    }

    .card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
    }

    .card-header {
        padding: 16px 18px 12px;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        flex-wrap: wrap;
    }

    .card-header h2 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .card-header p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }

    .card-body {
        padding: 16px 18px 18px;
    }

    .list {
        display: grid;
        gap: 10px;
    }

    .item {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
    }

    .item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .item-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .item-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 3px;
    }

    .item-meta {
        display: grid;
        gap: 4px;
        font-size: 13px;
        color: #334155;
        margin-top: 8px;
    }

    .item-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .item-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        background: #ffffff;
        border: 1px solid #dbe4ea;
        color: #334155;
    }

    .item-link.primary {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .badge-pending,
    .badge-under_review,
    .badge-rescheduled {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .badge-confirmed,
    .badge-checked_in,
    .badge-in_progress,
    .badge-completed {
        background: #ecfdf5;
        color: #15803d;
    }

    .badge-no_show,
    .badge-cancelled,
    .badge-rejected {
        background: #fef2f2;
        color: #dc2626;
    }

    .guest-tag,
    .returning-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        margin-left: 6px;
    }

    .guest-tag {
        background: #fff7ed;
        color: #c2410c;
    }

    .returning-tag {
        background: #ecfeff;
        color: #155e75;
    }

    .mini-note {
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }

    .empty {
        font-size: 14px;
        color: #64748b;
    }

    @media (max-width: 1100px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-page">
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>Staff Dashboard</h1>
            <p>Daily overview for appointment requests, appointments, and patient activity.</p>
        </div>

        <div class="dashboard-actions">
            <a href="{{ route('staff.appointment-requests.index') }}" class="action-btn primary">Request Queue</a>
            <a href="{{ route('staff.appointments.index', ['date' => now()->toDateString()]) }}" class="action-btn">Today's Schedule</a>
            <a href="{{ route('staff.appointments.create') }}" class="action-btn">Create Appointment</a>
            <a href="{{ route('staff.patients.index') }}" class="action-btn">Patients</a>
        </div>
    </div>

    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Pending Requests</div>
            <div class="stat-value">{{ $stats['pending_requests'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-value">{{ $stats['today_appointments'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Confirmed Upcoming</div>
            <div class="stat-value">{{ $stats['confirmed_upcoming'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Checked In Today</div>
            <div class="stat-value">{{ $stats['checked_in_today'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Completed Today</div>
            <div class="stat-value">{{ $stats['completed_today'] ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">No Show Today</div>
            <div class="stat-value">{{ $stats['no_show_today'] ?? 0 }}</div>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="card">
            <div class="card-header">
                <div>
                    <h2>Today's Appointments</h2>
                    <p>Appointments scheduled for today.</p>
                </div>
                <a href="{{ route('staff.appointments.index', ['date' => now()->toDateString()]) }}" class="item-link primary">View All</a>
            </div>

            <div class="card-body">
                @if(($todayAppointments ?? collect())->count())
                    <div class="list">
                        @foreach($todayAppointments as $appointment)
                            @php
                                $isGuestOnly = !$appointment->patient && $appointment->request;
                                $isReturning = (bool) $appointment->patient;

                                $patientName = $appointment->patient
                                    ? trim(($appointment->patient->first_name ?? '') . ' ' . ($appointment->patient->last_name ?? ''))
                                    : trim(($appointment->request?->guest_first_name ?? 'Guest') . ' ' . ($appointment->request?->guest_last_name ?? 'Patient'));

                                $dentistName = trim(
                                    ($appointment->dentist?->user?->first_name ?? 'Dentist') . ' ' .
                                    ($appointment->dentist?->user?->last_name ?? '')
                                );

                                $statusClass = 'badge-' . strtolower((string) ($appointment->status ?? 'pending'));
                                $patientSummary = $appointment->patient?->appointment_status_summary;
                            @endphp

                            <div class="item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">
                                            {{ $patientName ?: 'Guest Patient' }}

                                            @if($isGuestOnly)
                                                <span class="guest-tag">Guest</span>
                                            @elseif($isReturning)
                                                <span class="returning-tag">Patient</span>
                                            @endif
                                        </div>
                                        <div class="item-sub">{{ $appointment->appointment_code ?? '—' }}</div>
                                    </div>

                                    <span class="badge {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $appointment->status ?? 'pending') }}
                                    </span>
                                </div>

                                <div class="item-meta">
                                    <div><strong>Service:</strong> {{ $appointment->service?->service_name ?? '—' }}</div>
                                    <div><strong>Time:</strong> {{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }} - {{ $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') : '—' }}</div>
                                    <div><strong>Dentist:</strong> {{ $dentistName ?: 'Dentist' }}</div>
                                </div>

                                @if($appointment->patient && !empty($patientSummary))
                                    <div class="mini-note">
                                        Appointments: {{ $patientSummary['total_actual_appointments'] ?? 0 }} |
                                        Completed: {{ $patientSummary['statuses']['completed'] ?? 0 }} |
                                        No-show: {{ $patientSummary['statuses']['no_show'] ?? 0 }}
                                    </div>
                                @elseif($isGuestOnly)
                                    <div class="mini-note">
                                        Guest booking not yet linked to a patient record.
                                    </div>
                                @endif

                                <div class="item-actions">
                                    <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="item-link primary">View Appointment</a>

                                    @if($appointment->request_id)
                                        <a href="{{ route('staff.appointment-requests.show', $appointment->request_id) }}" class="item-link">Open Request</a>
                                    @endif

                                    @if($appointment->patient)
                                        <a href="{{ route('staff.patients.show', $appointment->patient->patient_id) }}" class="item-link">Open Patient</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">No appointments scheduled for today.</div>
                @endif
            </div>
        </section>

        <div class="side-grid">
            <section class="card">
                <div class="card-header">
                    <div>
                        <h2>Pending Requests</h2>
                        <p>Latest requests waiting for review.</p>
                    </div>
                    <a href="{{ route('staff.appointment-requests.index') }}" class="item-link primary">Open Queue</a>
                </div>

                <div class="card-body">
                    @if(($pendingRequests ?? collect())->count())
                        <div class="list">
                            @foreach($pendingRequests as $request)
                                @php
                                    $requestPatientName = $request->patient
                                        ? trim(($request->patient->first_name ?? '') . ' ' . ($request->patient->last_name ?? ''))
                                        : trim(($request->guest_first_name ?? '') . ' ' . ($request->guest_last_name ?? ''));

                                    $requestStatusClass = 'badge-' . strtolower((string) ($request->request_status ?? 'pending'));
                                @endphp

                                <div class="item">
                                    <div class="item-top">
                                        <div>
                                            <div class="item-title">
                                                {{ $requestPatientName ?: 'Guest Patient' }}
                                                @if(!$request->patient)
                                                    <span class="guest-tag">Guest</span>
                                                @else
                                                    <span class="returning-tag">Patient</span>
                                                @endif
                                            </div>
                                            <div class="item-sub">{{ $request->request_code ?? '—' }}</div>
                                        </div>

                                        <span class="badge {{ $requestStatusClass }}">
                                            {{ str_replace('_', ' ', $request->request_status ?? 'pending') }}
                                        </span>
                                    </div>

                                    <div class="item-meta">
                                        <div><strong>Service:</strong> {{ $request->service?->service_name ?? '—' }}</div>
                                        <div><strong>Date:</strong> {{ $request->preferred_date ? \Carbon\Carbon::parse($request->preferred_date)->format('M d, Y') : '—' }}</div>
                                        <div><strong>Time:</strong> {{ $request->preferred_start_time ? \Carbon\Carbon::parse($request->preferred_start_time)->format('h:i A') : '—' }}</div>
                                    </div>

                                    <div class="item-actions">
                                        <a href="{{ route('staff.appointment-requests.show', $request->request_id) }}" class="item-link primary">Review Request</a>

                                        @if($request->patient)
                                            <a href="{{ route('staff.patients.show', $request->patient->patient_id) }}" class="item-link">Open Patient</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">No pending appointment requests right now.</div>
                    @endif
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div>
                        <h2>Upcoming Appointments</h2>
                        <p>Next confirmed appointments after today.</p>
                    </div>
                </div>

                <div class="card-body">
                    @if(($upcomingAppointments ?? collect())->count())
                        <div class="list">
                            @foreach($upcomingAppointments as $appointment)
                                @php
                                    $upcomingPatient = $appointment->patient
                                        ? trim(($appointment->patient->first_name ?? '') . ' ' . ($appointment->patient->last_name ?? ''))
                                        : trim(($appointment->request?->guest_first_name ?? 'Guest') . ' ' . ($appointment->request?->guest_last_name ?? 'Patient'));

                                    $upcomingStatusClass = 'badge-' . strtolower((string) ($appointment->status ?? 'confirmed'));
                                @endphp

                                <div class="item">
                                    <div class="item-top">
                                        <div>
                                            <div class="item-title">
                                                {{ $upcomingPatient ?: 'Guest Patient' }}
                                                @if(!$appointment->patient && $appointment->request)
                                                    <span class="guest-tag">Guest</span>
                                                @elseif($appointment->patient)
                                                    <span class="returning-tag">Patient</span>
                                                @endif
                                            </div>
                                            <div class="item-sub">{{ $appointment->appointment_code ?? '—' }}</div>
                                        </div>

                                        <span class="badge {{ $upcomingStatusClass }}">
                                            {{ str_replace('_', ' ', $appointment->status ?? 'confirmed') }}
                                        </span>
                                    </div>

                                    <div class="item-meta">
                                        <div><strong>Service:</strong> {{ $appointment->service?->service_name ?? '—' }}</div>
                                        <div><strong>Date:</strong> {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '—' }}</div>
                                        <div><strong>Time:</strong> {{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }}</div>
                                    </div>

                                    <div class="item-actions">
                                        <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="item-link primary">View</a>

                                        @if($appointment->patient)
                                            <a href="{{ route('staff.patients.show', $appointment->patient->patient_id) }}" class="item-link">Open Patient</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">No upcoming appointments after today.</div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
