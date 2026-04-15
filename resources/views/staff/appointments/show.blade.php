@extends('layouts.app')

@section('content')
<div style="padding:32px; max-width:1100px; margin:0 auto;">
    <h1 style="font-size:28px; font-weight:800; margin-bottom:20px;">Appointment Details</h1>

    @if(session('success'))
        <div style="background:#dcfce7; color:#166534; padding:12px 16px; border-radius:12px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:12px 16px; border-radius:12px; margin-bottom:20px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
        <div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:20px;">
                <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Appointment Information</h2>

                <p><strong>Appointment Code:</strong> {{ $appointment->appointment_code }}</p>
                <p><strong>Date:</strong> {{ $appointment->appointment_date?->format('Y-m-d') }}</p>
                <p>
                    <strong>Time:</strong>
                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
                    -
                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                </p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</p>
                <p><strong>Arrival Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->arrival_status ?? 'pending')) }}</p>
                <p><strong>Dentist:</strong> {{ $appointment->dentist?->user?->first_name }} {{ $appointment->dentist?->user?->last_name }}</p>
                <p><strong>Service:</strong> {{ $appointment->service?->service_name }}</p>
                <p><strong>Remarks:</strong> {{ $appointment->remarks ?? '—' }}</p>
            </div>

            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px;">
                <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Status History</h2>

                @forelse($appointment->statusLogs as $log)
                    <div style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
                        <p>
                            <strong>{{ ucfirst(str_replace('_', ' ', $log->old_status ?? 'none')) }}</strong>
                            →
                            <strong>{{ ucfirst(str_replace('_', ' ', $log->new_status)) }}</strong>
                        </p>
                        <p>{{ $log->remarks }}</p>
                        <p style="color:#64748b; font-size:14px;">
                            {{ optional($log->changed_at)->format('Y-m-d h:i A') }}
                        </p>
                    </div>
                @empty
                    <p>No status logs yet.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:20px;">
                <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Patient Information</h2>

                @if($appointment->patient)
                    <p><strong>Patient Code:</strong> {{ $appointment->patient->patient_code ?? '—' }}</p>
                    <p>
                        <strong>Name:</strong>
                        {{ trim(($appointment->patient->first_name ?? '') . ' ' . ($appointment->patient->middle_name ?? '') . ' ' . ($appointment->patient->last_name ?? '')) }}
                    </p>
                    <p><strong>Sex:</strong> {{ ucfirst($appointment->patient->sex ?? '—') }}</p>
                    <p><strong>Birth Date:</strong> {{ $appointment->patient->birth_date ?? '—' }}</p>
                    <p><strong>Contact Number:</strong> {{ $appointment->patient->contact_number ?? '—' }}</p>
                    <p><strong>Email:</strong> {{ $appointment->patient->email ?? '—' }}</p>
                    <p><strong>Address:</strong> {{ $appointment->patient->address ?? '—' }}</p>
                @elseif($appointment->request)
                    <p><strong>Guest Name:</strong>
                        {{ trim(($appointment->request->guest_first_name ?? '') . ' ' . ($appointment->request->guest_middle_name ?? '') . ' ' . ($appointment->request->guest_last_name ?? '')) ?: '—' }}
                    </p>
                    <p><strong>Guest Contact:</strong> {{ $appointment->request->guest_contact_number ?? '—' }}</p>
                    <p><strong>Guest Email:</strong> {{ $appointment->request->guest_email ?? '—' }}</p>
                    <p style="color:#b45309; font-size:14px; margin-top:10px;">
                        This appointment is not yet linked to a permanent patient record.
                    </p>
                @else
                    <p>No patient or guest information available.</p>
                @endif
            </div>

            @if(!empty($patientSummary))
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:24px;">
                    <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Patient Appointment Summary</h2>

                    <p><strong>Total times set appointment:</strong> {{ $patientSummary['total_times_set_appointment'] }}</p>
                    <p><strong>Total actual appointments:</strong> {{ $patientSummary['total_actual_appointments'] }}</p>

                    <div style="margin-top:16px;">
                        <p style="font-weight:700; margin-bottom:10px;">Status Breakdown</p>
                        <p><strong>Pending:</strong> {{ $patientSummary['statuses']['pending'] }}</p>
                        <p><strong>Confirmed:</strong> {{ $patientSummary['statuses']['confirmed'] }}</p>
                        <p><strong>Checked-in:</strong> {{ $patientSummary['statuses']['checked_in'] }}</p>
                        <p><strong>Completed:</strong> {{ $patientSummary['statuses']['completed'] }}</p>
                        <p><strong>Rescheduled:</strong> {{ $patientSummary['statuses']['rescheduled'] }}</p>
                        <p><strong>Cancelled:</strong> {{ $patientSummary['statuses']['cancelled'] }}</p>
                        <p><strong>No-show:</strong> {{ $patientSummary['statuses']['no_show'] }}</p>
                        <p><strong>Rejected:</strong> {{ $patientSummary['statuses']['rejected'] }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
