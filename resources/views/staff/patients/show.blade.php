@extends('staff.layouts.app')

@section('content')
<style>
    .record {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px;
        display: grid;
        gap: 16px;
    }

    .card {
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:12px;
        padding:16px;
    }

    .header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        flex-wrap:wrap;
    }

    .name {
        font-size:22px;
        font-weight:800;
    }

    .sub {
        font-size:12px;
        color:#64748b;
    }

    .badge {
        padding:4px 10px;
        font-size:11px;
        border-radius:999px;
        font-weight:700;
        background:#ecfdf5;
        color:#166534;
    }

    .grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:16px;
    }

    .section-title {
        font-size:14px;
        font-weight:800;
        margin-bottom:10px;
    }

    .row {
        font-size:13px;
        margin-bottom:6px;
    }

    .summary {
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:10px;
    }

    .box {
        background:#f8fafc;
        border:1px solid #e2e8f0;
        border-radius:10px;
        padding:10px;
        text-align:center;
    }

    .box strong {
        font-size:18px;
        display:block;
    }

    .timeline {
        display:grid;
        gap:10px;
    }

    .item {
        border:1px solid #e2e8f0;
        border-radius:10px;
        padding:10px;
        font-size:13px;
    }

    .item-top {
        display:flex;
        justify-content:space-between;
        font-weight:700;
    }

    .meta {
        margin-top:6px;
        color:#475569;
    }

    .status {
        font-size:11px;
        padding:3px 8px;
        border-radius:999px;
        font-weight:700;
    }

    .completed { background:#ecfdf5; color:#15803d; }
    .confirmed { background:#eff6ff; color:#1d4ed8; }
    .no_show { background:#fef2f2; color:#dc2626; }
    .cancelled { background:#fef2f2; color:#dc2626; }

    @media(max-width:900px){
        .grid{grid-template-columns:1fr;}
        .summary{grid-template-columns:repeat(2,1fr);}
    }
</style>

@php
$name = trim(($patient->first_name ?? '').' '.($patient->last_name ?? ''));
@endphp

<div class="record">

    {{-- HEADER --}}
    <div class="card header">
        <div>
            <div class="name">{{ $name ?: 'Patient' }}</div>
            <div class="sub">Patient Code: {{ $patient->patient_code }}</div>
        </div>
        <div class="badge">{{ strtoupper($patient->profile_status ?? 'active') }}</div>
    </div>

    {{-- INFO --}}
    <div class="grid">

        <div class="card">
            <div class="section-title">Personal Information</div>

            <div class="row"><strong>Sex:</strong> {{ $patient->sex ?? '—' }}</div>
            <div class="row"><strong>Birth Date:</strong> {{ $patient->birth_date ?? '—' }}</div>
            <div class="row"><strong>Civil Status:</strong> {{ $patient->civil_status ?? '—' }}</div>
            <div class="row"><strong>Occupation:</strong> {{ $patient->occupation ?? '—' }}</div>
        </div>

        <div class="card">
            <div class="section-title">Contact Information</div>

            <div class="row"><strong>Phone:</strong> {{ $patient->contact_number ?? '—' }}</div>
            <div class="row"><strong>Email:</strong> {{ $patient->email ?? '—' }}</div>
            <div class="row"><strong>Address:</strong> {{ $patient->address ?? '—' }}</div>
        </div>


    </div>

    {{-- MEDICAL / NOTES --}}
    <div class="card">
        <div class="section-title">Notes / Clinical Remarks</div>
        <div class="row">{{ $patient->notes ?? 'No notes recorded.' }}</div>
    </div>

    {{-- SUMMARY --}}
    <div class="card">
        <div class="section-title">Appointment Summary</div>

        <div class="summary">
            <div class="box">
                <small>Visits</small>
                <strong>{{ $stats['total_actual_appointments'] ?? 0 }}</strong>
            </div>

            <div class="box">
                <small>Completed</small>
                <strong>{{ $stats['statuses']['completed'] ?? 0 }}</strong>
            </div>

            <div class="box">
                <small>No-show</small>
                <strong>{{ $stats['statuses']['no_show'] ?? 0 }}</strong>
            </div>

            <div class="box">
                <small>Cancelled</small>
                <strong>{{ $stats['statuses']['cancelled'] ?? 0 }}</strong>
            </div>
        </div>
    </div>

    {{-- TIMELINE --}}
    <div class="card">
        <div class="section-title">Appointment History</div>

        <div class="timeline">
            @forelse($patient->appointments as $appt)
                <div class="item">
                    <div class="item-top">
                        <span>{{ $appt->appointment_code }}</span>
                        <span class="status {{ strtolower($appt->status) }}">
                            {{ str_replace('_',' ',$appt->status) }}
                        </span>
                    </div>

                    <div class="meta">
                        {{ $appt->appointment_date }} |
                        {{ $appt->service?->service_name ?? '—' }} |
                        {{ $appt->start_time ? \Carbon\Carbon::parse($appt->start_time)->format('h:i A') : '' }}
                    </div>
                </div>
            @empty
                <div>No records found.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
