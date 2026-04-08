@extends('layouts.app')

@section('content')
@php
    $navItems = [
        [
            'label' => 'Dashboard',
            'url' => route('staff.dashboard'),
            'active' => request()->routeIs('staff.dashboard'),
            'enabled' => true,
        ],
        [
            'label' => 'Appointment Requests',
            'url' => route('staff.appointment-requests.index'),
            'active' => request()->routeIs('staff.appointment-requests.*'),
            'enabled' => true,
        ],
        [
            'label' => 'Daily Appointments',
            'url' => route('staff.appointments.index'),
            'active' => request()->routeIs('staff.appointments.*'),
            'enabled' => true,
        ],
        [
            'label' => 'Queue / Arrivals',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Patients',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Guest Conversion',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Billing / Payments',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Follow-Ups',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Messages',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Attachments / X-Rays',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Schedules',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
        [
            'label' => 'Audit / Activity',
            'url' => '#',
            'active' => false,
            'enabled' => false,
        ],
    ];
@endphp

<style>
    .staff-shell {
        min-height: 100vh;
        background: #f8fafc;
        padding: 24px;
    }

    .staff-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .staff-sidebar,
    .staff-panel,
    .staff-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .staff-sidebar {
        padding: 22px;
        height: fit-content;
        position: sticky;
        top: 24px;
    }

    .staff-brand {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .staff-subtitle {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 22px;
    }

    .staff-nav {
        display: grid;
        gap: 10px;
    }

    .staff-nav a,
    .staff-nav .disabled-link {
        display: block;
        padding: 12px 14px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
    }

    .staff-nav a {
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .staff-nav a:hover {
        border-color: #2563eb;
        color: #2563eb;
    }

    .staff-nav a.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .disabled-link {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .staff-main {
        display: grid;
        gap: 24px;
    }

    .staff-panel {
        padding: 28px;
    }

    .staff-heading {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .staff-text {
        color: #475569;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .staff-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
    }

    .staff-card {
        padding: 20px;
    }

    .staff-card-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .staff-card-value {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
    }

    .section-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .appointment-list {
        display: grid;
        gap: 14px;
    }

    .appointment-item {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        background: #f8fafc;
    }

    .appointment-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .appointment-time {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .appointment-status {
        padding: 6px 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 800;
    }

    .quick-links {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .quick-links a {
        display: inline-block;
        padding: 12px 16px;
        border-radius: 12px;
        background: #2563eb;
        color: white;
        text-decoration: none;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .staff-layout {
            grid-template-columns: 1fr;
        }

        .staff-sidebar {
            position: static;
        }
    }
</style>

<div class="staff-shell">
    <div class="staff-layout">
        <aside class="staff-sidebar">
            <div class="staff-brand">Staff Dashboard</div>
            <div class="staff-subtitle">
                Main staff workspace for appointment handling, clinic-day workflow, patient support, billing coordination, and records preparation.
            </div>

            <div class="staff-nav">
                @foreach($navItems as $item)
                    @if($item['enabled'])
                        <a href="{{ $item['url'] }}" class="{{ $item['active'] ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <div class="disabled-link">
                            {{ $item['label'] }} — Coming Soon
                        </div>
                    @endif
                @endforeach
            </div>
        </aside>

        <main class="staff-main">
            <section class="staff-panel">
                <div class="staff-heading">Welcome, Staff</div>
                <p class="staff-text">
                    Use this dashboard to monitor incoming appointment requests, manage today’s confirmed schedule,
                    handle arrivals and no-shows, and prepare for the next modules such as patients, billing, follow-ups, and messaging.
                </p>

                <div class="quick-links">
                    <a href="{{ route('staff.appointment-requests.index') }}">Open Appointment Requests</a>
                    <a href="{{ route('staff.appointments.index') }}">Open Daily Appointments</a>
                </div>
            </section>

            <section class="staff-stats">
                <div class="staff-card">
                    <div class="staff-card-label">Pending Requests</div>
                    <div class="staff-card-value">{{ $stats['pending_requests'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">Today's Appointments</div>
                    <div class="staff-card-value">{{ $stats['today_appointments'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">Confirmed Today</div>
                    <div class="staff-card-value">{{ $stats['today_confirmed'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">Checked In</div>
                    <div class="staff-card-value">{{ $stats['today_checked_in'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">In Progress</div>
                    <div class="staff-card-value">{{ $stats['today_in_progress'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">Completed</div>
                    <div class="staff-card-value">{{ $stats['today_completed'] }}</div>
                </div>

                <div class="staff-card">
                    <div class="staff-card-label">No Show</div>
                    <div class="staff-card-value">{{ $stats['today_no_show'] }}</div>
                </div>
            </section>

            <section class="staff-panel">
                <div class="section-title">Today’s Schedule Snapshot</div>

                <div class="appointment-list">
                    @forelse($todayAppointments as $appointment)
                        <div class="appointment-item">
                            <div class="appointment-top">
                                <div class="appointment-time">
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
                                    -
                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                </div>

                                <div class="appointment-status">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </div>
                            </div>

                            <div>
                                <strong>Patient:</strong>
                                {{ $appointment->patient?->first_name ?? 'Guest' }}
                                {{ $appointment->patient?->last_name ?? '' }}
                            </div>

                            <div>
                                <strong>Service:</strong>
                                {{ $appointment->service?->service_name ?? '—' }}
                            </div>

                            <div>
                                <strong>Dentist:</strong>
                                {{ $appointment->dentist?->user?->first_name ?? '—' }}
                                {{ $appointment->dentist?->user?->last_name ?? '' }}
                            </div>

                            <div style="margin-top:12px;">
                                <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}"
                                   style="display:inline-block; padding:10px 14px; background:#0f172a; color:white; border-radius:10px; text-decoration:none; font-weight:700;">
                                    View Appointment
                                </a>
                            </div>
                        </div>
                    @empty
                        <p style="color:#64748b; margin:0;">
                            No appointments scheduled for today yet.
                        </p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</div>
@endsection
