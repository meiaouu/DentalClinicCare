@extends('layouts.app')

@section('content')
    <div style="padding:32px; max-width:1100px; margin:0 auto;">
        <h1 style="font-size:28px; font-weight:800; margin-bottom:20px;">Review Appointment Request</h1>

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

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">
            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:24px;">
                <h2 style="font-size:22px; font-weight:700; margin-bottom:16px;">Request Details</h2>

                <p><strong>Request Code:</strong> {{ $requestItem->request_code }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $requestItem->request_status)) }}</p>
                <p><strong>Patient:</strong>
                    {{ $requestItem->patient?->first_name
                        ? $requestItem->patient->first_name . ' ' . $requestItem->patient->last_name
                        : trim(($requestItem->guest_first_name ?? '') . ' ' . ($requestItem->guest_last_name ?? '')) }}
                </p>
                <p><strong>Contact:</strong> {{ $requestItem->patient?->contact_number ?? $requestItem->guest_contact_number }}</p>
                <p><strong>Service:</strong> {{ $requestItem->service?->service_name }}</p>
                <p><strong>Requested Date:</strong> {{ $requestItem->preferred_date }}</p>
                <p><strong>Requested Time:</strong> {{ $requestItem->preferred_start_time }}</p>
                <p><strong>Preferred Dentist:</strong> {{ $requestItem->preferredDentist?->user?->full_name ?? 'Clinic will assign' }}</p>
                <p><strong>Notes:</strong> {{ $requestItem->notes ?? '—' }}</p>

                <hr style="margin:20px 0;">

                <h3 style="font-size:18px; font-weight:700; margin-bottom:12px;">Service Answers</h3>
                @if($requestItem->answers->count())
                    <ul style="padding-left:18px;">
                        @foreach($requestItem->answers as $answer)
                            <li>
                                <strong>{{ $answer->option?->option_name }}:</strong>
                                {{ $answer->selectedValue?->value_label ?? $answer->answer_text ?? '—' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No service answers found.</p>
                @endif
            </div>

            <div style="display:grid; gap:20px;">
                <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:20px;">
                    <h2 style="font-size:20px; font-weight:700; margin-bottom:12px;">Confirm Request</h2>

                    <form method="POST" action="{{ route('staff.appointment-requests.confirm', $requestItem->request_id) }}">
                        @csrf

                        <div style="margin-bottom:12px;">
                            <label>Dentist</label>
                            <select name="dentist_id" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                                <option value="">Select dentist</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->dentist_id }}" @selected((string) old('dentist_id', $requestItem->preferred_dentist_id) === (string) $dentist->dentist_id)>
                                        {{ $dentist->user?->full_name ?? 'Dentist #' . $dentist->dentist_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>Date</label>
                            <input type="date" name="appointment_date" value="{{ old('appointment_date', $requestItem->preferred_date) }}" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>Start Time</label>
                            <input type="time" name="start_time" value="{{ old('start_time', \Illuminate\Support\Str::of($requestItem->preferred_start_time)->substr(0,5)) }}" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>Remarks</label>
                            <textarea name="remarks" rows="3" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" style="width:100%; padding:12px; background:#16a34a; color:white; border:none; border-radius:12px; font-weight:700;">
                            Confirm and Create Appointment
                        </button>
                    </form>
                </div>

                <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:20px;">
                    <h2 style="font-size:20px; font-weight:700; margin-bottom:12px;">Reschedule Request</h2>

                    <form method="POST" action="{{ route('staff.appointment-requests.reschedule', $requestItem->request_id) }}">
                        @csrf

                        <div style="margin-bottom:12px;">
                            <label>Dentist</label>
                            <select name="dentist_id" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                                <option value="">Select dentist</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->dentist_id }}">
                                        {{ $dentist->user?->full_name ?? 'Dentist #' . $dentist->dentist_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>New Date</label>
                            <input type="date" name="preferred_date" value="{{ old('preferred_date', $requestItem->preferred_date) }}" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>New Time</label>
                            <input type="time" name="preferred_start_time" value="{{ old('preferred_start_time', \Illuminate\Support\Str::of($requestItem->preferred_start_time)->substr(0,5)) }}" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label>Remarks</label>
                            <textarea name="remarks" rows="3" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px;">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" style="width:100%; padding:12px; background:#f59e0b; color:white; border:none; border-radius:12px; font-weight:700;">
                            Save Reschedule
                        </button>
                    </form>
                </div>

                <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:20px;">
                    <h2 style="font-size:20px; font-weight:700; margin-bottom:12px;">Reject Request</h2>

                    <form method="POST" action="{{ route('staff.appointment-requests.reject', $requestItem->request_id) }}">
                        @csrf
                        <textarea name="remarks" rows="3" placeholder="Reason for rejection" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:12px; margin-bottom:12px;">{{ old('remarks') }}</textarea>

                        <button type="submit" style="width:100%; padding:12px; background:#dc2626; color:white; border:none; border-radius:12px; font-weight:700;">
                            Reject Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
