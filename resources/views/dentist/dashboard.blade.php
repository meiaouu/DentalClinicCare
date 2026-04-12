@extends('dentist.layouts.app')

@section('page_title', 'Dashboard')

@section('dentist_content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3"><strong>{{ $stats['today_appointments'] }}</strong><div>Today’s Appointments</div></div></div>
    <div class="col-md-3"><div class="card p-3"><strong>{{ $stats['today_patients'] }}</strong><div>Patients Today</div></div></div>
    <div class="col-md-3"><div class="card p-3"><strong>{{ $stats['pending_followups'] }}</strong><div>Pending Follow-Ups</div></div></div>
    <div class="col-md-3"><div class="card p-3"><strong>{{ $stats['completed_treatments'] }}</strong><div>Completed Treatments</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-3">
            <h3>Today’s Appointments</h3>
            @forelse($todayAppointments as $appointment)
                <div class="border-bottom py-2">
                    <strong>{{ $appointment->start_time }} - {{ $appointment->end_time }}</strong>
                    <div>{{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}</div>
                    <small>{{ $appointment->service?->service_name }} · {{ ucfirst($appointment->status) }}</small>
                </div>
            @empty
                <p class="mb-0">No appointments for today.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-3 mb-3">
            <h3>Upcoming Schedule</h3>
            @forelse($upcomingAppointments as $appointment)
                <div class="border-bottom py-2">
                    <strong>{{ $appointment->appointment_date }} {{ $appointment->start_time }}</strong>
                    <div>{{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}</div>
                    <small>{{ $appointment->service?->service_name }}</small>
                </div>
            @empty
                <p class="mb-0">No upcoming appointments.</p>
            @endforelse
        </div>

        <div class="card p-3">
            <h3>New vs Returning</h3>
            <p class="mb-1">New: {{ $newVsReturning['new'] }}</p>
            <p class="mb-0">Returning: {{ $newVsReturning['returning'] }}</p>
        </div>
    </div>
</div>
@endsection
