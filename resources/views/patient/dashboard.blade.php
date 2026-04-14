@extends('patient.layouts.app')

@section('content')
<style>
    .patient-dashboard-page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 32px 16px 60px;
    }

    .patient-dashboard-container {
        max-width: 1150px;
        margin: 0 auto;
        display: grid;
        gap: 18px;
    }

    .patient-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 24px;
    }

    .patient-header h1 {
        margin: 0 0 6px;
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
    }

    .patient-header p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.7;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 18px;
    }

    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 22px;
    }

    .dashboard-card h2 {
        margin: 0 0 6px;
        font-size: 21px;
        font-weight: 800;
        color: #0f172a;
    }

    .card-note {
        margin: 0 0 16px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
    }

    .appointment-highlight {
        border: 1px solid #ccfbf1;
        background: #f0fdfa;
        border-radius: 18px;
        padding: 18px;
    }

    .appointment-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .appointment-meta {
        font-size: 14px;
        color: #334155;
        line-height: 1.8;
    }

    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .quick-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
    }

    .quick-btn-primary {
        background: #0f9d8a;
        color: #ffffff;
    }

    .quick-btn-secondary {
        background: #f1f5f9;
        color: #334155;
    }

    .list-grid {
        display: grid;
        gap: 12px;
    }

    .list-item {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 16px;
        padding: 14px;
    }

    .list-item-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .list-item-meta {
        font-size: 13px;
        color: #475569;
        line-height: 1.7;
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
        margin-top: 8px;
    }

    .badge-pending,
    .badge-under_review {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .badge-confirmed,
    .badge-rescheduled,
    .badge-checked_in,
    .badge-completed {
        background: #ecfdf3;
        color: #15803d;
    }

    .badge-cancelled,
    .badge-rejected,
    .badge-no_show {
        background: #fef2f2;
        color: #dc2626;
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 1000px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            flex-direction: column;
        }

        .quick-btn {
            width: 100%;
        }
    }
</style>

<div class="patient-dashboard-page">
    <div class="patient-dashboard-container">
        <div class="patient-header">
            <h1>
                Welcome, {{ $user?->first_name ?? 'Patient' }}
            </h1>
            <p>
                View your upcoming appointments, recent requests, follow-up reminders, and important clinic information here.
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Pending Requests</div>
                <div class="stat-value">{{ $stats['pending_requests'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Upcoming Appointments</div>
                <div class="stat-value">{{ $stats['upcoming_appointments'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Completed Visits</div>
                <div class="stat-value">{{ $stats['completed_appointments'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Follow-Ups</div>
                <div class="stat-value">{{ $stats['follow_ups'] }}</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div style="display:grid; gap:18px;">
                <div class="dashboard-card">
                    <h2>Upcoming Appointment</h2>
                    <p class="card-note">Your next confirmed clinic visit.</p>

                    @if($upcomingAppointment)
                        <div class="appointment-highlight">
                            <div class="appointment-title">
                                {{ $upcomingAppointment->service?->service_name ?? 'Appointment' }}
                            </div>

                            <div class="appointment-meta">
                                <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($upcomingAppointment->appointment_date)->format('M d, Y') }}<br>

                                <strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($upcomingAppointment->start_time)->format('h:i A') }}
                                -
                                {{ \Carbon\Carbon::parse($upcomingAppointment->end_time)->format('h:i A') }}<br>

                                <strong>Dentist:</strong>
                                {{ trim(($upcomingAppointment->dentist?->user?->first_name ?? '') . ' ' . ($upcomingAppointment->dentist?->user?->last_name ?? '')) ?: 'Clinic assigned dentist' }}<br>

                                <strong>Status:</strong>
                                {{ ucfirst(str_replace('_', ' ', $upcomingAppointment->status)) }}
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            No upcoming appointment found.
                        </div>
                    @endif
                </div>

                <div class="dashboard-card">
                    <h2>Recent Appointment Requests</h2>
                    <p class="card-note">Latest requests you submitted to the clinic.</p>

                    <div class="list-grid">
                        @forelse($recentRequests as $request)
                            <div class="list-item">
                                <div class="list-item-title">
                                    {{ $request->service?->service_name ?? 'Service Request' }}
                                </div>

                                <div class="list-item-meta">
                                    <strong>Date Requested:</strong>
                                    {{ $request->preferred_date ? \Carbon\Carbon::parse($request->preferred_date)->format('M d, Y') : '—' }}<br>

                                    <strong>Preferred Time:</strong>
                                    {{ $request->preferred_start_time ? \Carbon\Carbon::parse($request->preferred_start_time)->format('h:i A') : '—' }}
                                </div>

                                <span class="badge badge-{{ $request->request_status }}">
                                    {{ str_replace('_', ' ', $request->request_status) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">
                                No appointment requests found yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-card">
                    <h2>Recent Appointments</h2>
                    <p class="card-note">Your recent clinic visits and appointment records.</p>

                    <div class="list-grid">
                        @forelse($recentAppointments as $appointment)
                            <div class="list-item">
                                <div class="list-item-title">
                                    {{ $appointment->service?->service_name ?? 'Appointment' }}
                                </div>

                                <div class="list-item-meta">
                                    <strong>Date:</strong>
                                    {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '—' }}<br>

                                    <strong>Time:</strong>
                                    {{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }}<br>

                                    <strong>Dentist:</strong>
                                    {{ trim(($appointment->dentist?->user?->first_name ?? '') . ' ' . ($appointment->dentist?->user?->last_name ?? '')) ?: 'Clinic assigned dentist' }}
                                </div>

                                <span class="badge badge-{{ $appointment->status }}">
                                    {{ str_replace('_', ' ', $appointment->status) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">
                                No appointment history found yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div style="display:grid; gap:18px;">
                <div class="dashboard-card">
                    <h2>Quick Actions</h2>
                    <p class="card-note">Fast access to the most important patient actions.</p>

                    <div class="quick-actions">
                        <a href="{{ route('booking.create') }}" class="quick-btn quick-btn-primary">Book Appointment</a>
                        <a href="{{ route('home') }}" class="quick-btn quick-btn-secondary">Clinic Home</a>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h2>Follow-Up Reminders</h2>
                    <p class="card-note">Recommended next visits from the clinic.</p>

                    <div class="list-grid">
                        @forelse($followUps as $followUp)
                            <div class="list-item">
                                <div class="list-item-title">
                                    Follow-Up Visit
                                </div>

                                <div class="list-item-meta">
                                    <strong>Recommended Date:</strong>
                                    {{ $followUp->recommended_date ? \Carbon\Carbon::parse($followUp->recommended_date)->format('M d, Y') : '—' }}<br>

                                    <strong>Reason:</strong>
                                    {{ $followUp->reason ?: 'No reason provided' }}<br>

                                    <strong>Status:</strong>
                                    {{ ucfirst(str_replace('_', ' ', $followUp->status ?? 'pending')) }}
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                No follow-up reminders found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-card">
                    <h2>Profile Summary</h2>
                    <p class="card-note">Basic information linked to your patient account.</p>

                    <div class="list-item" style="background:#f8fafc;">
                        <div class="list-item-meta">
                            <strong>Name:</strong>
                            {{ trim(($patient->first_name ?? '') . ' ' . ($patient->middle_name ?? '') . ' ' . ($patient->last_name ?? '')) ?: ($user?->first_name . ' ' . $user?->last_name) }}<br>

                            <strong>Contact Number:</strong>
                            {{ $patient->contact_number ?? $user?->contact_number ?? '—' }}<br>

                            <strong>Email:</strong>
                            {{ $patient->email ?? $user?->email ?? '—' }}<br>

                            <strong>Profile Status:</strong>
                            {{ $patient->profile_status ?? 'Active' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
