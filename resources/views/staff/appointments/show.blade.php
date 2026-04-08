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

    <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:20px;">
        <p><strong>Appointment Code:</strong> {{ $appointment->appointment_code }}</p>
        <p><strong>Date:</strong> {{ $appointment->appointment_date?->format('Y-m-d') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</p>
        <p><strong>Arrival Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->arrival_status ?? 'pending')) }}</p>
        <p><strong>Patient:</strong> {{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}</p>
        <p><strong>Dentist:</strong> {{ $appointment->dentist?->user?->first_name }} {{ $appointment->dentist?->user?->last_name }}</p>
        <p><strong>Service:</strong> {{ $appointment->service?->service_name }}</p>
        <p><strong>Remarks:</strong> {{ $appointment->remarks ?? '—' }}</p>
    </div>

    <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px;">
        <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Status History</h2>

        @forelse($appointment->statusLogs as $log)
            <div style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
                <p><strong>{{ ucfirst(str_replace('_', ' ', $log->old_status ?? 'none')) }}</strong> → <strong>{{ ucfirst(str_replace('_', ' ', $log->new_status)) }}</strong></p>
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
@endsection
