@extends('staff.layouts.app')

@section('content')
    <div style="padding:32px;">
        <h1 style="font-size:28px; font-weight:800; margin-bottom:20px;">Appointment Request Queue</h1>

        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:12px 16px; border-radius:12px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display:grid; gap:16px;">
            @forelse($requests as $request)
                <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:20px;">
                    <p><strong>Request Code:</strong> {{ $request->request_code }}</p>
                    <p><strong>Patient:</strong>
                        {{ $request->patient?->first_name
                            ? $request->patient->first_name . ' ' . $request->patient->last_name
                            : trim(($request->guest_first_name ?? '') . ' ' . ($request->guest_last_name ?? '')) }}
                    </p>
                    <p><strong>Service:</strong> {{ $request->service?->service_name }}</p>
                    <p><strong>Date:</strong> {{ $request->preferred_date }}</p>
                    <p><strong>Time:</strong> {{ $request->preferred_start_time }}</p>
                    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $request->request_status)) }}</p>

                    <div style="margin-top:12px;">
                        <a href="{{ route('staff.appointment-requests.show', $request->request_id) }}"
                           style="display:inline-block; padding:10px 16px; background:#2563eb; color:white; border-radius:999px; text-decoration:none;">
                            Review Request
                        </a>
                    </div>
                </div>
            @empty
                <p>No pending appointment requests.</p>
            @endforelse
        </div>

        <div style="margin-top:20px;">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
