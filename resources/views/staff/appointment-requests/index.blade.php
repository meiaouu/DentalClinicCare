@extends('layouts.app')

@section('content')
    <div style="max-width:1200px; margin:40px auto; padding:0 20px;">
        <h1 style="font-size:32px; font-weight:800; margin-bottom:20px;">Appointment Request Queue</h1>

        <div style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">
            @foreach (['pending', 'under_review', 'rescheduled', 'rejected', 'converted_to_appointment', 'all'] as $filter)
                <a href="{{ route('staff.appointment-requests.index', ['status' => $filter]) }}"
                   style="padding:10px 16px; border-radius:999px; border:1px solid #cbd5e1; color:#0f172a; text-decoration:none;">
                    {{ ucfirst(str_replace('_', ' ', $filter)) }}
                </a>
            @endforeach
        </div>

        <div style="background:white; border-radius:20px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,0.05);">
            @forelse ($requests as $request)
                <div style="padding:18px 0; border-bottom:1px solid #e2e8f0;">
                    <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:700;">{{ $request->request_code }}</div>
                            <div>{{ $request->guest_first_name }} {{ $request->guest_last_name }}</div>
                            <div style="color:#475569;">{{ $request->service?->service_name }}</div>
                            <div style="color:#475569;">{{ $request->preferred_date?->format('Y-m-d') }} {{ $request->preferred_start_time }}</div>
                            <div style="color:#475569;">Status: {{ $request->request_status }}</div>
                        </div>

                        <div>
                            <a href="{{ route('staff.appointment-requests.show', $request->request_id) }}"
                               style="padding:10px 16px; background:#2563eb; color:white; border-radius:999px; text-decoration:none;">
                                Review
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>No appointment requests found.</p>
            @endforelse

            <div style="margin-top:20px;">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
