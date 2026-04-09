@extends('dentist.layouts.app')

@section('content')
    <h1 style="margin-top:0;">Dentist Dashboard</h1>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">
        <div class="card">
            <h2 style="margin-top:0;">Today's Appointments</h2>

            @forelse($todayAppointments as $appointment)
                <div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:12px;">
                    <strong>{{ $appointment->start_time }} - {{ $appointment->end_time }}</strong><br>
                    Patient:
                    {{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}<br>
                    Service: {{ $appointment->service?->service_name }}<br>
                    Status: {{ ucfirst($appointment->status) }}
                </div>
            @empty
                <p>No appointments for today.</p>
            @endforelse
        </div>

        <div class="card">
            <h2 style="margin-top:0;">Quick Links</h2>
            <a href="{{ route('dentist.availability.index') }}">Manage Availability</a>
        </div>
    </div>
@endsection
