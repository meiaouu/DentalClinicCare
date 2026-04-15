@extends('staff.layouts.app')

@section('content')
<style>
    .dashboard-page {
        display: grid;
        gap: 20px;
    }

    .welcome-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .welcome-label {
        font-size: 13px;
        font-weight: 700;
        color: #0f766e;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
        animation: fadeSlideIn 0.7s ease;
    }

    .welcome-title {
        margin: 0 0 6px;
        font-size: 30px;
        font-weight: 800;
        color: #111827;
        animation: fadeSlideIn 0.9s ease;
    }

    .welcome-subtitle {
        margin: 0;
        font-size: 14px;
        color: #6b7280;
        animation: fadeSlideIn 1s ease;
    }

    .welcome-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .welcome-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .welcome-btn.primary {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        padding: 18px 20px;
        min-height: 115px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .summary-card * {
        position: relative;
        z-index: 2;
    }

    .summary-card::before,
    .summary-card::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        z-index: 1;
    }

    .summary-card::before {
        width: 140px;
        height: 140px;
        top: -50px;
        right: -40px;
        background: rgba(255, 255, 255, 0.08);
    }

    .summary-card::after {
        width: 90px;
        height: 90px;
        bottom: -35px;
        right: 10px;
        background: rgba(0, 0, 0, 0.18);
    }

    .summary-card.one {
        background: linear-gradient(135deg, #111827, #1f2937);
    }

    .summary-card.two {
        background: linear-gradient(135deg, #374151, #4b5563);
    }

    .summary-card.three {
        background: linear-gradient(135deg, #0f9d8a, #06b6d4);
    }

    .summary-number {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1;
        color: #ffffff;
    }

    .summary-title {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #ffffff;
    }

    .summary-text {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.5;
    }

    .top-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 2px;
    }

    .top-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 16px;
        border-radius: 10px 10px 0 0;
        border: 1px solid #e5e7eb;
        border-bottom: none;
        background: #f9fafb;
        color: #4b5563;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .top-tab.active {
        background: #ffffff;
        color: #111827;
        box-shadow: inset 0 -2px 0 #0f9d8a;
    }

    .section-box {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
    }

    .section-header {
        padding: 18px 20px 14px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-header h2 {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 800;
        color: #111827;
    }

    .section-header p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    .section-link {
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .dashboard-table th {
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        padding: 14px 20px;
        border-bottom: 1px solid #eef2f7;
        background: #fafafa;
    }

    .dashboard-table td {
        font-size: 13px;
        color: #374151;
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    .dashboard-table tr:last-child td {
        border-bottom: none;
    }

    .person-name {
        font-weight: 700;
        color: #111827;
        margin-bottom: 3px;
    }

    .person-sub {
        font-size: 12px;
        color: #6b7280;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 26px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-pending,
    .status-under_review,
    .status-rescheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-confirmed,
    .status-checked_in,
    .status-in_progress,
    .status-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .status-no_show,
    .status-cancelled,
    .status-rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .guest-badge,
    .patient-badge {
        display: inline-block;
        margin-top: 4px;
        padding: 3px 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .guest-badge {
        background: #fff7ed;
        color: #c2410c;
    }

    .patient-badge {
        background: #ecfeff;
        color: #0f766e;
    }

    .mini-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .mini-actions a {
        text-decoration: none;
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 12px;
        color: #374151;
        background: #ffffff;
    }

    .mini-actions a.primary {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .empty-row {
        padding: 18px 20px;
        font-size: 14px;
        color: #6b7280;
    }

    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 900px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-page">
    <div class="welcome-panel">
        <div>
            <div class="welcome-label">Hello</div>
            <h1 class="welcome-title">Welcome!</h1>
            <p class="welcome-subtitle">This is your daily overview.</p>
        </div>

        <div class="welcome-actions">
            <a href="{{ route('staff.appointment-requests.index') }}" class="welcome-btn primary">Request Queue</a>
            <a href="{{ route('staff.appointments.index', ['date' => now()->toDateString()]) }}" class="welcome-btn">Today's Schedule</a>
            <a href="{{ route('staff.appointments.create') }}" class="welcome-btn">Create Appointment</a>
            <a href="{{ route('staff.patients.index') }}" class="welcome-btn">Patients</a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card one">
            <div class="summary-number">{{ $stats['pending_requests'] ?? 0 }}</div>
            <div class="summary-title">Pending Requests</div>
            <div class="summary-text">Requests waiting for staff review and decision.</div>
        </div>

        <div class="summary-card two">
            <div class="summary-number">{{ $stats['today_appointments'] ?? 0 }}</div>
            <div class="summary-title">Today's Appointments</div>
            <div class="summary-text">Appointments scheduled for today.</div>
        </div>

        <div class="summary-card three">
            <div class="summary-number">{{ $stats['confirmed_upcoming'] ?? 0 }}</div>
            <div class="summary-title">Confirmed Upcoming</div>
            <div class="summary-text">Confirmed appointments after today.</div>
        </div>
    </div>

    <div class="top-tabs">
        <button type="button" class="top-tab active" data-tab="today">
            Today's Appointments ({{ ($todayAppointments ?? collect())->count() }})
        </button>

        <button type="button" class="top-tab" data-tab="requests">
            Appointment Requests ({{ ($pendingRequests ?? collect())->count() }})
        </button>

        <button type="button" class="top-tab" data-tab="upcoming">
            Upcoming Appointments ({{ ($upcomingAppointments ?? collect())->count() }})
        </button>
    </div>

    <div class="section-box tab-section" id="tab-today">
        <div class="section-header">
            <div>
                <h2>Today's Appointments</h2>
            </div>
            <a href="{{ route('staff.appointments.index', ['date' => now()->toDateString()]) }}" class="section-link">View all</a>
        </div>

        @if(($todayAppointments ?? collect())->count())
            <div class="table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Time</th>
                            <th>Dentist</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
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

                                $statusClass = 'status-' . strtolower((string) ($appointment->status ?? 'pending'));
                            @endphp

                            <tr>
                                <td>
                                    <div class="person-name">{{ $patientName ?: 'Guest Patient' }}</div>
                                    <div class="person-sub">{{ $appointment->appointment_code ?? '—' }}</div>

                                    @if($isGuestOnly)
                                        <div class="guest-badge">Guest</div>
                                    @elseif($isReturning)
                                        <div class="patient-badge">Patient</div>
                                    @endif
                                </td>
                                <td>{{ $appointment->service?->service_name ?? '—' }}</td>
                                <td>
                                    {{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }}
                                    -
                                    {{ $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') : '—' }}
                                </td>
                                <td>{{ $dentistName ?: 'Dentist' }}</td>
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $appointment->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mini-actions">
                                        <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="primary">View</a>

                                        @if($appointment->request_id)
                                            <a href="{{ route('staff.appointment-requests.show', $appointment->request_id) }}">Request</a>
                                        @endif

                                        @if($appointment->patient)
                                            <a href="{{ route('staff.patients.show', $appointment->patient->patient_id) }}">Patient</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-row">No appointments scheduled for today.</div>
        @endif
    </div>

    <div class="section-box tab-section" id="tab-requests" style="display:none;">
        <div class="section-header">
            <div>
                <h2>Appointment Requests</h2>
            </div>
            <a href="{{ route('staff.appointment-requests.index') }}" class="section-link">View all</a>
        </div>

        @if(($pendingRequests ?? collect())->count())
            <div class="table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                            @php
                                $requestPatientName = $request->patient
                                    ? trim(($request->patient->first_name ?? '') . ' ' . ($request->patient->last_name ?? ''))
                                    : trim(($request->guest_first_name ?? '') . ' ' . ($request->guest_last_name ?? ''));

                                $requestStatusClass = 'status-' . strtolower((string) ($request->request_status ?? 'pending'));
                            @endphp

                            <tr>
                                <td>
                                    <div class="person-name">{{ $requestPatientName ?: 'Guest Patient' }}</div>
                                    <div class="person-sub">{{ $request->request_code ?? '—' }}</div>

                                    @if(!$request->patient)
                                        <div class="guest-badge">Guest</div>
                                    @else
                                        <div class="patient-badge">Patient</div>
                                    @endif
                                </td>
                                <td>{{ $request->service?->service_name ?? '—' }}</td>
                                <td>{{ $request->preferred_date ? \Carbon\Carbon::parse($request->preferred_date)->format('M d, Y') : '—' }}</td>
                                <td>{{ $request->preferred_start_time ? \Carbon\Carbon::parse($request->preferred_start_time)->format('h:i A') : '—' }}</td>
                                <td>
                                    <span class="status-pill {{ $requestStatusClass }}">
                                        {{ str_replace('_', ' ', $request->request_status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mini-actions">
                                        <a href="{{ route('staff.appointment-requests.show', $request->request_id) }}" class="primary">Review</a>

                                        @if($request->patient)
                                            <a href="{{ route('staff.patients.show', $request->patient->patient_id) }}">Patient</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-row">No pending appointment requests right now.</div>
        @endif
    </div>

    <div class="section-box tab-section" id="tab-upcoming" style="display:none;">
        <div class="section-header">
            <div>
                <h2>Upcoming Appointments</h2>
                <p>Next confirmed appointments after today.</p>
            </div>
        </div>

        @if(($upcomingAppointments ?? collect())->count())
            <div class="table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingAppointments as $appointment)
                            @php
                                $upcomingPatient = $appointment->patient
                                    ? trim(($appointment->patient->first_name ?? '') . ' ' . ($appointment->patient->last_name ?? ''))
                                    : trim(($appointment->request?->guest_first_name ?? 'Guest') . ' ' . ($appointment->request?->guest_last_name ?? 'Patient'));

                                $upcomingStatusClass = 'status-' . strtolower((string) ($appointment->status ?? 'confirmed'));
                            @endphp

                            <tr>
                                <td>
                                    <div class="person-name">{{ $upcomingPatient ?: 'Guest Patient' }}</div>
                                    <div class="person-sub">{{ $appointment->appointment_code ?? '—' }}</div>

                                    @if(!$appointment->patient && $appointment->request)
                                        <div class="guest-badge">Guest</div>
                                    @elseif($appointment->patient)
                                        <div class="patient-badge">Patient</div>
                                    @endif
                                </td>
                                <td>{{ $appointment->service?->service_name ?? '—' }}</td>
                                <td>{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '—' }}</td>
                                <td>{{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }}</td>
                                <td>
                                    <span class="status-pill {{ $upcomingStatusClass }}">
                                        {{ str_replace('_', ' ', $appointment->status ?? 'confirmed') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mini-actions">
                                        <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="primary">View</a>

                                        @if($appointment->patient)
                                            <a href="{{ route('staff.patients.show', $appointment->patient->patient_id) }}">Patient</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-row">No upcoming appointments after today.</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.top-tab');
    const sections = {
        today: document.getElementById('tab-today'),
        requests: document.getElementById('tab-requests'),
        upcoming: document.getElementById('tab-upcoming'),
    };

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            Object.values(sections).forEach(function (section) {
                if (section) {
                    section.style.display = 'none';
                }
            });

            const key = this.dataset.tab;

            if (sections[key]) {
                sections[key].style.display = 'block';
            }
        });
    });
});
</script>
@endsection
