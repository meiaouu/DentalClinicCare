@extends('layouts.app')

@section('content')
    <div style="max-width:1200px; margin:40px auto; padding:0 20px;">
        <h1 style="font-size:32px; font-weight:800; margin-bottom:20px;">
            Review Request {{ $appointmentRequest->request_code }}
        </h1>

        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:14px 16px; border-radius:12px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#fee2e2; color:#991b1b; padding:14px 16px; border-radius:12px; margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">
            <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,0.05);">
                <h2 style="font-size:24px; font-weight:700; margin-bottom:16px;">Request Details</h2>

                <p><strong>Status:</strong> {{ $appointmentRequest->request_status }}</p>
                <p><strong>Patient:</strong> {{ $appointmentRequest->guest_first_name }} {{ $appointmentRequest->guest_last_name }}</p>
                <p><strong>Contact:</strong> {{ $appointmentRequest->guest_contact_number }}</p>
                <p><strong>Service:</strong> {{ $appointmentRequest->service?->service_name }}</p>
                <p><strong>Requested Date:</strong> {{ $appointmentRequest->preferred_date?->format('Y-m-d') }}</p>
                <p><strong>Requested Time:</strong> {{ $appointmentRequest->preferred_start_time }}</p>
                <p><strong>Preferred Dentist:</strong> {{ $appointmentRequest->preferredDentist?->user?->full_name ?? 'Clinic assigned' }}</p>
                <p><strong>Address:</strong> {{ $appointmentRequest->address }}</p>
                <p><strong>Notes:</strong> {{ $appointmentRequest->notes ?? '—' }}</p>

                <hr style="margin:20px 0;">

                <h3 style="font-size:20px; font-weight:700; margin-bottom:12px;">Service Answers</h3>
                @forelse($appointmentRequest->answers as $answer)
                    <p>
                        <strong>{{ $answer->option?->option_name }}:</strong>
                        {{ $answer->selectedValue?->value_label ?? $answer->answer_text ?? '—' }}
                    </p>
                @empty
                    <p>No answers submitted.</p>
                @endforelse

                @if($appointmentRequest->convertedAppointment)
                    <hr style="margin:20px 0;">
                    <h3 style="font-size:20px; font-weight:700; margin-bottom:12px;">Converted Appointment</h3>
                    <p><strong>Appointment ID:</strong> {{ $appointmentRequest->convertedAppointment->appointment_id }}</p>
                    <p><strong>Status:</strong> {{ $appointmentRequest->convertedAppointment->status }}</p>
                    <p><strong>Date:</strong> {{ $appointmentRequest->convertedAppointment->appointment_date?->format('Y-m-d') }}</p>
                    <p><strong>Time:</strong> {{ $appointmentRequest->convertedAppointment->start_time }} - {{ $appointmentRequest->convertedAppointment->end_time }}</p>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div style="background:white; border-radius:20px; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,0.05);">
                    <h3 style="font-size:20px; font-weight:700; margin-bottom:12px;">Confirm Request</h3>
                    <form method="POST" action="{{ route('staff.appointment-requests.confirm', $appointmentRequest->request_id) }}">
                        @csrf

                        <div style="margin-bottom:12px;">
                            <label>Dentist</label>
                            <select name="dentist_id" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:10px;">
                                <option value="">Select dentist</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->user_id }}" {{ old('dentist_id') == $dentist->user_id ? 'selected' : '' }}>
                                        {{ $dentist->user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" style="padding:10px 16px; background:#2563eb; color:white; border:none; border-radius:10px; cursor:pointer;">Confirm Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
