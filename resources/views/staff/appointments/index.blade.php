@extends('layouts.app')

@section('content')
<div style="padding:32px;">
    <h1 style="font-size:28px; font-weight:800; margin-bottom:20px;">Daily Appointments</h1>

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

    <form method="GET" action="{{ route('staff.appointments.index') }}" style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
        <input type="date" name="date" value="{{ $date }}" style="padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px;">
        <select name="status" style="padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px;">
            <option value="">All Statuses</option>
            <option value="confirmed" @selected($status === 'confirmed')>Confirmed</option>
            <option value="checked_in" @selected($status === 'checked_in')>Checked In</option>
            <option value="in_progress" @selected($status === 'in_progress')>In Progress</option>
            <option value="completed" @selected($status === 'completed')>Completed</option>
            <option value="no_show" @selected($status === 'no_show')>No Show</option>
            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
        </select>
        <button type="submit" style="padding:10px 16px; background:#2563eb; color:white; border:none; border-radius:10px;">Filter</button>
    </form>

    <div style="display:grid; gap:16px;">
        @forelse($appointments as $appointment)
            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:20px;">
                <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</p>
                        <p><strong>Patient:</strong> {{ $appointment->patient?->first_name }} {{ $appointment->patient?->last_name }}</p>
                        <p><strong>Service:</strong> {{ $appointment->service?->service_name }}</p>
                        <p><strong>Dentist:</strong> {{ $appointment->dentist?->user?->first_name }} {{ $appointment->dentist?->user?->last_name }}</p>
                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</p>
                        <p><strong>Arrival Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->arrival_status ?? 'pending')) }}</p>
                    </div>

                    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-start;">
                        <a href="{{ route('staff.appointments.show', $appointment->appointment_id) }}"
                           style="padding:10px 14px; background:#0f172a; color:white; border-radius:10px; text-decoration:none;">
                            View
                        </a>

                        <form method="POST" action="{{ route('staff.appointments.arrived', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" style="padding:10px 14px; background:#f59e0b; color:white; border:none; border-radius:10px;">
                                Arrived
                            </button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.checkin', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" style="padding:10px 14px; background:#2563eb; color:white; border:none; border-radius:10px;">
                                Check In
                            </button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.inprogress', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" style="padding:10px 14px; background:#7c3aed; color:white; border:none; border-radius:10px;">
                                In Progress
                            </button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.complete', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" style="padding:10px 14px; background:#16a34a; color:white; border:none; border-radius:10px;">
                                Complete
                            </button>
                        </form>

                        <form method="POST" action="{{ route('staff.appointments.noshow', $appointment->appointment_id) }}">
                            @csrf
                            <button type="submit" style="padding:10px 14px; background:#dc2626; color:white; border:none; border-radius:10px;">
                                No Show
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No appointments found for this day.</p>
        @endforelse
    </div>

    <div style="margin-top:20px;">
        {{ $appointments->links() }}
    </div>
</div>
@endsection
