@extends('staff.layouts.app')

@section('content')
<style>
    .appointments-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .appointments-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .appointments-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .appointments-filter {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        padding: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }

    .appointments-filter input,
    .appointments-filter select {
        height: 42px;
        padding: 0 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        font-size: 14px;
    }

    .filter-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #334155;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
    }

    .filter-note {
        width: 100%;
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .alert-box {
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #ecfdf5;
        color: #166534;
        border-color: #bbf7d0;
    }

    .alert-danger {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 18px;
    }

    .appointments-list {
        display: grid;
        gap: 12px;
    }

    .appointment-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
    }

    .appointment-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .appointment-patient {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .appointment-sub {
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
    }

    .badge-wrap {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    .badge-confirmed,
    .badge-rescheduled,
    .badge-checked_in,
    .badge-in_progress,
    .badge-completed {
        background: #ecfdf3;
        color: #15803d;
    }

    .badge-no_show,
    .badge-cancelled {
        background: #fef2f2;
        color: #dc2626;
    }

    .badge-pending {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .badge-arrival {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .badge-arrived {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-checkedin {
        background: #ecfdf3;
        color: #15803d;
    }

    .badge-noshow {
        background: #fef2f2;
        color: #dc2626;
    }

    .appointment-details {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .detail-box {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 12px;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.5;
    }

    .appointment-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-link,
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .action-link {
        background: #0f172a;
        color: #ffffff;
        border: none;
    }

    .action-btn {
        border: none;
        color: #ffffff;
    }

    .btn-arrived { background: #f59e0b; }
    .btn-checkin { background: #2563eb; }
    .btn-progress { background: #7c3aed; }
    .btn-complete { background: #16a34a; }
    .btn-noshow { background: #dc2626; }
    .btn-cancel {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .request-link {
        background: #eff6ff;
        color: #2563eb;
    }

    .empty-state {
        padding: 24px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 900px) {
        .appointment-details {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 560px) {
        .appointment-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="appointments-page">
    <div class="appointments-header">
        <h1>Daily Appointments</h1>
        <p>View appointments by date, track patient status, and update progress.</p>
    </div>
@if (Route::has('staff.appointments.create'))
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
        <a href="{{ route('staff.appointments.create') }}"
           style="display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 16px; border-radius:10px; background:#0f9d8a; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
            Create Appointment
        </a>
    </div>
@endif
    @if(session('success'))
        <div class="alert-box alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('staff.appointments.index') }}" class="appointments-filter" id="appointmentsFilterForm">
        <input
            type="date"
            name="date"
            value="{{ $date }}"
            onchange="document.getElementById('appointmentsFilterForm').submit()"
        >

        <select
            name="status"
            onchange="document.getElementById('appointmentsFilterForm').submit()"
        >
            <option value="">All Statuses</option>
            <option value="confirmed" @selected($status === 'confirmed')>Confirmed</option>
            <option value="rescheduled" @selected($status === 'rescheduled')>Rescheduled</option>
            <option value="checked_in" @selected($status === 'checked_in')>Checked In</option>
            <option value="in_progress" @selected($status === 'in_progress')>In Progress</option>
            <option value="completed" @selected($status === 'completed')>Completed</option>
            <option value="no_show" @selected($status === 'no_show')>No Show</option>
            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
        </select>

        <a href="{{ route('staff.appointments.index') }}" class="filter-reset">Reset</a>

        <div class="filter-note">
            Filters apply automatically when you change the date or status.
        </div>
    </form>

    <div class="appointments-list">
        @forelse($appointments as $appointment)
            @php
                $patientName = trim(
                    ($appointment->patient?->first_name ?? 'Guest') . ' ' .
                    ($appointment->patient?->last_name ?? 'Patient')
                );

                $dentistName = trim(
                    ($appointment->dentist?->user?->first_name ?? 'Dentist') . ' ' .
                    ($appointment->dentist?->user?->last_name ?? '')
                );

                $statusKey = strtolower((string) ($appointment->status ?? 'pending'));
                $statusClass = 'badge-' . $statusKey;

                $arrivalStatus = strtolower((string) ($appointment->arrival_status ?? 'pending'));
                $arrivalClass = match ($arrivalStatus) {
                    'arrived' => 'badge-arrived',
                    'checked_in' => 'badge-checkedin',
                    'no_show' => 'badge-noshow',
                    default => 'badge-arrival',
                };
            @endphp

            <div class="appointment-card">
                <div class="appointment-top">
                    <div>
                        <h2 class="appointment-patient">{{ $patientName }}</h2>
                        <div class="appointment-sub">
                            {{ $appointment->appointment_code ?? 'No appointment code' }}
                        </div>
                    </div>

                    <div class="badge-wrap">
                        <span class="badge {{ $statusClass }}">
                            {{ str_replace('_', ' ', $appointment->status ?? 'pending') }}
                        </span>

                        <span class="badge {{ $arrivalClass }}">
                            {{ str_replace('_', ' ', $appointment->arrival_status ?? 'pending') }}
                        </span>
                    </div>
                </div>

                <div class="appointment-details">
                    <div class="detail-box">
                        <div class="detail-label">Time</div>
                        <div class="detail-value">
                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
                            -
                            {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                        </div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Service</div>
                        <div class="detail-value">{{ $appointment->service?->service_name ?? '—' }}</div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Dentist</div>
                        <div class="detail-value">{{ $dentistName ?: 'Dentist' }}</div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">
                            {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '—' }}
                        </div>
                    </div>
                </div>

                <div class="appointment-actions">
                    <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}" class="action-link">
                        View
                    </a>

                    @if(in_array($appointment->status, ['confirmed', 'rescheduled'], true))
                        <form method="POST" action="{{ route('staff.appointments.arrived', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-arrived">Arrived</button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.checkin', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-checkin">Check In</button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.noshow', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-noshow">No Show</button>
                        </form>
                    @endif

                    @if($appointment->status === 'checked_in')
                        <form method="POST" action="{{ route('staff.appointments.inprogress', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-progress">In Progress</button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.complete', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-complete">Complete</button>
                        </form>
                    @endif

                    @if($appointment->status === 'in_progress')
                        <form method="POST" action="{{ route('staff.appointments.complete', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-complete">Complete</button>
                        </form>
                    @endif

                    @if(!in_array($appointment->status, ['completed', 'cancelled', 'no_show'], true))
                        <form method="POST" action="{{ route('staff.appointments.cancel', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" class="action-btn btn-cancel">Cancel</button>
                        </form>
                    @endif

                    @if($appointment->request_id)
                        <a href="{{ route('staff.appointment-requests.show', $appointment->request_id) }}" class="action-link request-link">
                            Open Request
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                No appointments found for the selected date and status.
            </div>
        @endforelse
    </div>

    <div style="margin-top: 8px;">
        {{ $appointments->links() }}
    </div>
</div>
@endsection
