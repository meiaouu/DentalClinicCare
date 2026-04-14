@extends('staff.layouts.app')

@section('content')
@php
    $payload = [];

    if (!empty($requestItem->notes)) {
        $decoded = json_decode($requestItem->notes, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $payload = $decoded;
        }
    }

    $patientInfo = $payload['patient_info'] ?? [];
    $addressInfo = $payload['address'] ?? [];
    $notesOrConcerns = $payload['notes_or_concerns'] ?? null;

    $displayPatientName = $requestItem->patient
        ? trim(($requestItem->patient->first_name ?? '') . ' ' . ($requestItem->patient->last_name ?? ''))
        : trim(
            ($requestItem->guest_first_name ?: ($patientInfo['first_name'] ?? '')) . ' ' .
            ($requestItem->guest_last_name ?: ($patientInfo['last_name'] ?? ''))
        );

    $displayContact = $requestItem->patient?->contact_number
        ?? $requestItem->guest_contact_number
        ?? ($patientInfo['contact_number'] ?? '—');

    $displayEmail = $requestItem->patient?->email
        ?? $requestItem->guest_email
        ?? ($patientInfo['email'] ?? '—');

    $displayPreferredDentist = $requestItem->preferredDentist?->user?->full_name ?? 'Clinic will assign';
    $hasConvertedAppointment = !empty($requestItem->convertedAppointment);

    $requestedDate = !empty($requestItem->preferred_date)
        ? \Carbon\Carbon::parse($requestItem->preferred_date)->toDateString()
        : '';

    $requestedTime = !empty($requestItem->preferred_start_time)
        ? \Illuminate\Support\Str::of($requestItem->preferred_start_time)->substr(0, 5)
        : '';

    $oldConfirmDate = old('appointment_date', $requestedDate);
    $oldConfirmTime = old('start_time', $requestedTime);
    $oldConfirmDentistId = old('dentist_id', (string) ($requestItem->preferred_dentist_id ?? ''));

    $oldRescheduleDate = old('preferred_date', $requestedDate);
    $oldRescheduleTime = old('preferred_start_time', $requestedTime);

    $status = strtolower((string) $requestItem->request_status);
    $statusStyle = match($status) {
        'pending' => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
        'under_review' => 'background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;',
        'converted_to_appointment' => 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;',
        'rejected' => 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;',
        default => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;',
    };
@endphp

<style>
    .request-page {
        padding: 32px;
        max-width: 1240px;
        margin: 0 auto;
        background: #f8fafc;
    }

    .request-header {
        margin-bottom: 24px;
    }

    .request-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .request-subtitle {
        color: #64748b;
        margin: 0;
        font-size: 14px;
    }

    .alert-box {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 20px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #ecfdf5;
        color: #166534;
        border-color: #bbf7d0;
    }

    .alert-danger {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .converted-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .request-grid {
        display: grid;
        grid-template-columns: 1.65fr 1fr;
        gap: 22px;
        align-items: start;
    }

    .panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .panel + .panel {
        margin-top: 18px;
    }

    .panel-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eef2f7;
    }

    .panel-subtitle {
        margin: -4px 0 14px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .info-item {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        background: #f8fafc;
        padding: 13px;
    }

    .info-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.6;
        word-break: break-word;
    }

    .summary-list {
        display: grid;
        gap: 8px;
    }

    .summary-row {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-key {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
    }

    .summary-value {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.6;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .answer-list {
        margin: 0;
        padding-left: 18px;
    }

    .answer-list li {
        margin-bottom: 8px;
        color: #334155;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select,
    textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #ffffff;
        box-sizing: border-box;
        outline: none;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #0f9d8a;
        box-shadow: 0 0 0 3px rgba(15, 157, 138, 0.10);
    }

    textarea {
        min-height: 96px;
        resize: vertical;
    }

    .btn {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-success { background: #16a34a; }
    .btn-warning { background: #f59e0b; }
    .btn-danger { background: #dc2626; }

    .helper-text {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
    }

    .inline-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin: 12px 0 14px;
    }

    .inline-btn {
        border: none;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .inline-btn-edit {
        background: #e2e8f0;
        color: #0f172a;
    }

    .inline-btn-reset {
        background: #fef3c7;
        color: #92400e;
    }

    .edit-block {
        display: none;
        margin-top: 12px;
        padding: 14px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
    }

    .edit-block.active {
        display: block;
    }

    .dentist-highlight {
        border: 2px solid #0f9d8a;
        background: linear-gradient(180deg, #ecfdf5 0%, #f8fffb 100%);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .dentist-highlight-label {
        font-size: 12px;
        font-weight: 800;
        color: #0f766e;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: .04em;
    }

    .dentist-highlight-value {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .availability-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .availability-tag {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #334155;
    }

    .availability-tag.primary {
        background: #ecfdf5;
        color: #0f766e;
        border-color: #a7f3d0;
    }

    .availability-tag.muted {
        background: #f8fafc;
        color: #64748b;
        border-color: #e2e8f0;
    }

    .week-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .week-label {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .week-nav {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid #d7e0e8;
        background: #fff;
        color: #334155;
        font-weight: 800;
        cursor: pointer;
    }

    .week-strip-wrap {
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .week-strip {
        display: grid;
        grid-template-columns: repeat(7, minmax(86px, 1fr));
        gap: 10px;
        min-width: 670px;
    }

    .week-day-card {
        border: 1px solid #dbe4ea;
        background: #ffffff;
        border-radius: 14px;
        padding: 12px 10px;
        text-align: center;
        cursor: pointer;
        transition: .2s ease;
    }

    .week-day-card:hover:not(.disabled) {
        border-color: #0f9d8a;
        background: #f0fdfa;
    }

    .week-day-card.active {
        border-color: #0f9d8a;
        background: #ecfdf5;
        box-shadow: inset 0 0 0 1px #0f9d8a;
    }

    .week-day-card.disabled {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: .8;
    }

    .week-day-name {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 5px;
    }

    .week-day-card.active .week-day-name {
        color: #0f766e;
    }

    .week-day-number {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .week-day-card.disabled .week-day-number {
        color: #94a3b8;
    }

    .week-day-sub {
        margin-top: 6px;
        font-size: 11px;
        color: #64748b;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .time-slot-btn {
        border: 1px solid #dbe4ea;
        background: #fff;
        border-radius: 12px;
        min-height: 44px;
        padding: 10px;
        font-weight: 700;
        text-align: center;
        color: #334155;
        cursor: pointer;
        transition: .2s ease;
    }

    .time-slot-btn:hover:not(.disabled) {
        border-color: #0f9d8a;
        color: #0f9d8a;
    }

    .time-slot-btn.active {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #fff;
    }

    .time-slot-btn.disabled {
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    @media (max-width: 991px) {
        .request-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .summary-row {
            grid-template-columns: 1fr;
        }

        .slot-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="request-page">
    <div class="request-header">
        <h1 class="request-title">Review Appointment Request</h1>
        <p class="request-subtitle">Review request details and assign the final dentist, date, and time.</p>
    </div>

    @if(session('success'))
        <div class="alert-box alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-danger">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($hasConvertedAppointment)
        <div class="converted-box">
            This request has already been converted into an appointment.
            <br>
            <strong>Appointment Code:</strong> {{ $requestItem->convertedAppointment->appointment_code ?? 'N/A' }}
        </div>
    @endif

    <div class="request-grid">
        <div>
            <div class="panel">
                <h2 class="panel-title">Request Overview</h2>

                <div class="summary-list">
                    <div class="summary-row">
                        <div class="summary-key">Request Code</div>
                        <div class="summary-value">{{ $requestItem->request_code }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Status</div>
                        <div class="summary-value">
                            <span class="status-badge" style="@php echo $statusStyle; @endphp">
                                {{ ucfirst(str_replace('_', ' ', $requestItem->request_status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Patient</div>
                        <div class="summary-value">{{ $displayPatientName ?: '—' }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Contact Number</div>
                        <div class="summary-value">{{ $displayContact }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Email</div>
                        <div class="summary-value">{{ $displayEmail }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Service</div>
                        <div class="summary-value">{{ $requestItem->service?->service_name ?? '—' }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Requested Date</div>
                        <div class="summary-value">{{ $requestedDate ?: '—' }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Requested Time</div>
                        <div class="summary-value">{{ $requestedTime ?: '—' }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Preferred Dentist</div>
                        <div class="summary-value">{{ $displayPreferredDentist }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-key">Patient Concerns</div>
                        <div class="summary-value">{{ $notesOrConcerns ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Patient Information</h2>

                <div class="info-grid">
                    <div class="info-item"><div class="info-label">First Name</div><div class="info-value">{{ $patientInfo['first_name'] ?? $requestItem->guest_first_name ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Middle Name</div><div class="info-value">{{ $patientInfo['middle_name'] ?? $requestItem->guest_middle_name ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Last Name</div><div class="info-value">{{ $patientInfo['last_name'] ?? $requestItem->guest_last_name ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Sex</div><div class="info-value">{{ $patientInfo['sex'] ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Birth Date</div><div class="info-value">{{ $patientInfo['birth_date'] ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Civil Status</div><div class="info-value">{{ $patientInfo['civil_status'] ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Occupation</div><div class="info-value">{{ $patientInfo['occupation'] ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Emergency Contact</div><div class="info-value">{{ $patientInfo['emergency_contact_name'] ?? '—' }}</div></div>
                    <div class="info-item"><div class="info-label">Emergency Number</div><div class="info-value">{{ $patientInfo['emergency_contact_number'] ?? '—' }}</div></div>

                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">
                            {{ trim(
                                ($addressInfo['address_line'] ?? '') .
                                (empty($addressInfo['address_line']) ? '' : ', ') .
                                ($addressInfo['barangay'] ?? '') .
                                (empty($addressInfo['barangay']) ? '' : ', ') .
                                ($addressInfo['city'] ?? '') .
                                (empty($addressInfo['city']) ? '' : ', ') .
                                ($addressInfo['province'] ?? '') .
                                (empty($addressInfo['province']) ? '' : ', ') .
                                ($addressInfo['region'] ?? '')
                            , ', ') ?: '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Service Answers</h2>
                @if($requestItem->answers->count())
                    <ul class="answer-list">
                        @foreach($requestItem->answers as $answer)
                            <li>
                                <strong>{{ $answer->option?->option_name ?? 'Question' }}:</strong>
                                {{ $answer->selectedValue?->value_label ?? $answer->answer_text ?? '—' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="margin:0; color:#64748b;">No service answers found.</p>
                @endif
            </div>
        </div>

        <div>
            @if(!$hasConvertedAppointment)
                <div class="panel">
                    <h2 class="panel-title">Confirm Appointment</h2>
                    <p class="panel-subtitle">Select the dentist first, then pick a date. Time slots load automatically when a date is clicked.</p>

                    <div class="dentist-highlight">
                        <div class="dentist-highlight-label">Assigned Dentist</div>
                        <div class="dentist-highlight-value" id="approvedDentistLabel">{{ $displayPreferredDentist }}</div>

                        <div class="availability-tags">
                            <span class="availability-tag primary" id="availabilityDentistTag">Dentist selection required</span>
                            <span class="availability-tag muted" id="availabilityModeTag">Availability tag pending</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('staff.appointment-requests.confirm', $requestItem->request_id) }}" id="confirmAppointmentForm">
                        @csrf

                        <input type="hidden" name="dentist_id" id="confirm_dentist_id" value="{{ $oldConfirmDentistId }}">
                        <input type="hidden" name="appointment_date" id="confirm_appointment_date" value="{{ $oldConfirmDate }}">
                        <input type="hidden" name="start_time" id="confirm_start_time" value="{{ $oldConfirmTime }}">

                        <div class="form-group">
                            <label class="form-label">Select Dentist</label>
                            <select id="confirmDentistSelect" class="form-select">
                                <option value="">Select dentist</option>
                                @foreach($dentists as $dentist)
                                    <option
                                        value="{{ $dentist->dentist_id }}"
                                        data-label="{{ $dentist->user?->full_name ?? ('Dentist #' . $dentist->dentist_id) }}"
                                        @selected((string) $oldConfirmDentistId === (string) $dentist->dentist_id)
                                    >
                                        {{ $dentist->user?->full_name ?? ('Dentist #' . $dentist->dentist_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="summary-list" style="margin:14px 0;">
                            <div class="summary-row">
                                <div class="summary-key">Approved Date</div>
                                <div class="summary-value" id="approvedDateLabel">{{ $oldConfirmDate ?: '—' }}</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-key">Approved Time</div>
                                <div class="summary-value" id="approvedTimeLabel">{{ $oldConfirmTime ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="inline-actions">
                            <button type="button" class="inline-btn inline-btn-edit" data-edit-target="dateEditBlock">Edit Date & Time</button>
                            <button type="button" class="inline-btn inline-btn-reset" id="resetConfirmValuesBtn">Reset</button>
                        </div>

                        <div class="edit-block active" id="dateEditBlock">
                            <div class="week-toolbar">
                                <button type="button" class="week-nav" id="prevWeekBtn">&lt;</button>
                                <h6 id="weekRangeLabel" class="week-label">Select Week</h6>
                                <button type="button" class="week-nav" id="nextWeekBtn">&gt;</button>
                            </div>

                            <div class="week-strip-wrap">
                                <div id="staffWeekStrip" class="week-strip"></div>
                            </div>

                            <div class="helper-text" style="margin-top:12px;">
                                Selected date: <strong id="calendarSelectedDateText">{{ $oldConfirmDate ?: 'None' }}</strong>
                            </div>

                            <div style="margin-top:14px;">
                                <label class="form-label">Available Time Slots</label>
                                <div id="confirmTimeSlotGrid" class="slot-grid"></div>
                                <div id="confirmSlotFeedback" class="helper-text" style="margin-top:10px;">
                                    Select a date to load available time slots.
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:14px;">
                            <label class="form-label">Staff Remarks</label>
                            <textarea name="remarks">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Confirm Appointment</button>
                    </form>
                </div>

                <div class="panel">
                    <h2 class="panel-title">Reschedule Request</h2>

                    <form method="POST" action="{{ route('staff.appointment-requests.reschedule', $requestItem->request_id) }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Assign Dentist</label>
                            <select name="dentist_id" class="form-select" required>
                                <option value="">Select dentist</option>
                                @foreach($dentists as $dentist)
                                    <option
                                        value="{{ $dentist->dentist_id }}"
                                        @selected((string) old('dentist_id', $oldConfirmDentistId) === (string) $dentist->dentist_id)
                                    >
                                        {{ $dentist->user?->full_name ?? ('Dentist #' . $dentist->dentist_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Appointment Date</label>
                            <input type="date" name="preferred_date" class="form-control" value="{{ $oldRescheduleDate }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Start Time</label>
                            <input type="time" name="preferred_start_time" class="form-control" value="{{ $oldRescheduleTime }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning">Save Reschedule</button>
                    </form>
                </div>

                <div class="panel">
                    <h2 class="panel-title">Reject Request</h2>

                    <form method="POST" action="{{ route('staff.appointment-requests.reject', $requestItem->request_id) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Reason for Rejection</label>
                            <textarea name="remarks" placeholder="Enter reason for rejection">{{ old('remarks') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Reject Request</button>
                    </form>

                </div>
            @else
                <div class="panel">
                    <h2 class="panel-title">Request Actions</h2>
                    <p style="margin:0; color:#64748b; line-height:1.7;">
                        This request already has a converted appointment record, so confirm, reschedule,
                        and reject actions are hidden to prevent duplicate processing.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@if(!$hasConvertedAppointment)
    @php
        $requestDefaults = [
            'dentistId' => (string) ($requestItem->preferred_dentist_id ?? ''),
            'dentistLabel' => $displayPreferredDentist,
            'date' => $requestedDate,
            'time' => (string) $requestedTime,
            'serviceId' => (int) ($requestItem->service_id ?? 0),
        ];
    @endphp

    <div
        id="requestDefaultsData"
        data-request-defaults='@json($requestDefaults)'
        hidden
    ></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const requestDefaultsEl = document.getElementById('requestDefaultsData');
    const requestDefaults = JSON.parse(requestDefaultsEl?.dataset.requestDefaults || '{}');

    const confirmDentistId = document.getElementById('confirm_dentist_id');
    const confirmAppointmentDate = document.getElementById('confirm_appointment_date');
    const confirmStartTime = document.getElementById('confirm_start_time');

    const approvedDentistLabel = document.getElementById('approvedDentistLabel');
    const approvedDateLabel = document.getElementById('approvedDateLabel');
    const approvedTimeLabel = document.getElementById('approvedTimeLabel');

    const availabilityDentistTag = document.getElementById('availabilityDentistTag');
    const availabilityModeTag = document.getElementById('availabilityModeTag');

    const confirmDentistSelect = document.getElementById('confirmDentistSelect');
    const confirmTimeSlotGrid = document.getElementById('confirmTimeSlotGrid');
    const confirmSlotFeedback = document.getElementById('confirmSlotFeedback');

    const staffWeekStrip = document.getElementById('staffWeekStrip');
    const weekRangeLabel = document.getElementById('weekRangeLabel');
    const calendarSelectedDateText = document.getElementById('calendarSelectedDateText');
    const prevWeekBtn = document.getElementById('prevWeekBtn');
    const nextWeekBtn = document.getElementById('nextWeekBtn');

    const toggleButtons = document.querySelectorAll('[data-edit-target]');
    const resetConfirmValuesBtn = document.getElementById('resetConfirmValuesBtn');

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const now = new Date();

    let selectedDate = confirmAppointmentDate.value
        ? new Date(confirmAppointmentDate.value + 'T00:00:00')
        : null;

    function startOfWeek(date) {
        const d = new Date(date);
        const day = d.getDay();
        d.setDate(d.getDate() - day);
        d.setHours(0, 0, 0, 0);
        return d;
    }

    let currentWeekStart = startOfWeek(selectedDate || today);

    function normalizeTime(value) {
        if (!value) return '';
        return value.length === 5 ? value + ':00' : value;
    }

    function formatDateLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDayShort(date) {
        return date.toLocaleDateString('en-US', { weekday: 'short' });
    }

    function formatMonthShort(date) {
        return date.toLocaleDateString('en-US', { month: 'short' });
    }

    function formatWeekRange(startDate) {
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 6);

        const startText = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const endText = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        return `${startText} - ${endText}`;
    }

    function formatHourLabel(time24) {
        const [hour, minute] = time24.split(':');
        const h = parseInt(hour, 10);
        const suffix = h >= 12 ? 'PM' : 'AM';
        const displayHour = ((h + 11) % 12 + 1).toString().padStart(2, '0');
        return `${suffix} ${displayHour}:${minute}`;
    }

    function updateSummaryLabels() {
        approvedDateLabel.textContent = confirmAppointmentDate.value || '—';
        approvedTimeLabel.textContent = confirmStartTime.value || '—';

        if (confirmDentistSelect && confirmDentistSelect.value) {
            const option = confirmDentistSelect.options[confirmDentistSelect.selectedIndex];
            approvedDentistLabel.textContent = option?.dataset.label || option?.textContent || '—';
            availabilityDentistTag.textContent = 'Dentist selected';
            availabilityDentistTag.className = 'availability-tag primary';
        } else {
            approvedDentistLabel.textContent = requestDefaults.dentistLabel || 'Clinic will assign';
            availabilityDentistTag.textContent = 'Select dentist first';
            availabilityDentistTag.className = 'availability-tag muted';
        }

        calendarSelectedDateText.textContent = confirmAppointmentDate.value || 'None';
    }

    function toggleEditBlock(targetId) {
        const el = document.getElementById(targetId);
        if (!el) return;
        el.classList.toggle('active');
    }

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            toggleEditBlock(this.dataset.editTarget);
        });
    });

    if (confirmDentistSelect) {
        confirmDentistSelect.addEventListener('change', function () {
            confirmDentistId.value = this.value;
            confirmStartTime.value = '';
            updateSummaryLabels();
            loadAvailableSlots();
        });
    }

    resetConfirmValuesBtn.addEventListener('click', function () {
        confirmDentistId.value = requestDefaults.dentistId;
        confirmAppointmentDate.value = requestDefaults.date;
        confirmStartTime.value = requestDefaults.time;

        if (confirmDentistSelect) {
            confirmDentistSelect.value = requestDefaults.dentistId;
        }

        selectedDate = requestDefaults.date ? new Date(requestDefaults.date + 'T00:00:00') : null;
        currentWeekStart = startOfWeek(selectedDate || today);

        renderWeekStrip();
        updateSummaryLabels();
        loadAvailableSlots();
    });

    function renderWeekStrip() {
        staffWeekStrip.innerHTML = '';
        weekRangeLabel.textContent = formatWeekRange(currentWeekStart);

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(currentWeekStart);
            dayDate.setDate(currentWeekStart.getDate() + i);
            dayDate.setHours(0, 0, 0, 0);

            const dateStr = formatDateLocal(dayDate);
            const isPast = dayDate < today;
            const isSelected = selectedDate && formatDateLocal(selectedDate) === dateStr;

            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'week-day-card';
            if (isPast) {
                card.classList.add('disabled');
                card.disabled = true;
            }
            if (isSelected) {
                card.classList.add('active');
            }

            card.innerHTML = `
                <div class="week-day-name">${formatDayShort(dayDate)}</div>
                <div class="week-day-number">${dayDate.getDate()}</div>
                <div class="week-day-sub">${formatMonthShort(dayDate)}</div>
            `;

            if (!isPast) {
                card.addEventListener('click', function () {
                    selectedDate = dayDate;
                    confirmAppointmentDate.value = dateStr;
                    confirmStartTime.value = '';
                    updateSummaryLabels();
                    renderWeekStrip();
                    loadAvailableSlots();
                });
            }

            staffWeekStrip.appendChild(card);
        }
    }

    function isSameLocalDate(a, b) {
        return a.getFullYear() === b.getFullYear()
            && a.getMonth() === b.getMonth()
            && a.getDate() === b.getDate();
    }

    async function loadAvailableSlots() {
        confirmTimeSlotGrid.innerHTML = '';

        if (!requestDefaults.serviceId || !confirmAppointmentDate.value) {
            confirmSlotFeedback.textContent = 'Select a dentist and date to load available time slots.';
            availabilityModeTag.textContent = 'Availability tag pending';
            availabilityModeTag.className = 'availability-tag muted';
            return;
        }

        if (!confirmDentistId.value) {
            confirmSlotFeedback.textContent = 'Select a dentist first before choosing a time.';
            availabilityModeTag.textContent = 'Dentist not selected';
            availabilityModeTag.className = 'availability-tag muted';
            return;
        }

        let url = `{{ route('booking.available.slots') }}?service_id=${encodeURIComponent(requestDefaults.serviceId)}&date=${encodeURIComponent(confirmAppointmentDate.value)}&dentist_id=${encodeURIComponent(confirmDentistId.value)}`;

        try {
            confirmSlotFeedback.textContent = 'Loading available time slots...';

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load slots');
            }

            const result = await response.json();
            const slots = Array.isArray(result.available_slots) ? result.available_slots : [];
            const clinicHours = Array.isArray(result.clinic_hours) ? result.clinic_hours : [];

            const availabilityTag = result.availability_tag || '';
            if (availabilityTag) {
                availabilityModeTag.textContent = availabilityTag;
                availabilityModeTag.className = 'availability-tag primary';
            } else {
                availabilityModeTag.textContent = slots.length > 0 ? 'Dentist available' : 'No available slots';
                availabilityModeTag.className = slots.length > 0 ? 'availability-tag primary' : 'availability-tag muted';
            }

            if (clinicHours.length === 0) {
                confirmSlotFeedback.textContent = 'No clinic hours configured for this date.';
                return;
            }

            const availableMap = {};
            slots.forEach((slot) => {
                availableMap[normalizeTime(slot.start_time)] = slot;
            });

            const selectedDateObj = new Date(confirmAppointmentDate.value + 'T00:00:00');
            const isTodaySelected = isSameLocalDate(selectedDateObj, now);

            clinicHours.forEach((hour) => {
                const normalizedHour = normalizeTime(hour);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.textContent = formatHourLabel(normalizedHour);

                const [hh, mm, ss] = normalizedHour.split(':').map(Number);
                const slotDateTime = new Date(selectedDateObj);
                slotDateTime.setHours(hh, mm, ss || 0, 0);

                const isPastSlot = isTodaySelected && slotDateTime <= now;

                if (availableMap[normalizedHour] && !isPastSlot) {
                    btn.dataset.value = normalizedHour;

                    if ((confirmStartTime.value + ':00') === normalizedHour || confirmStartTime.value === normalizedHour) {
                        btn.classList.add('active');
                    }

                    btn.addEventListener('click', function () {
                        document.querySelectorAll('#confirmTimeSlotGrid .time-slot-btn').forEach(el => el.classList.remove('active'));
                        btn.classList.add('active');
                        confirmStartTime.value = normalizedHour.slice(0, 5);
                        updateSummaryLabels();
                    });
                } else {
                    btn.classList.add('disabled');
                    btn.disabled = true;
                }

                confirmTimeSlotGrid.appendChild(btn);
            });

            confirmSlotFeedback.textContent = slots.length > 0
                ? `${slots.length} available time slot(s) found.`
                : 'No available slots for the selected date.';
        } catch (error) {
            confirmSlotFeedback.textContent = 'Failed to load available slots.';
            availabilityModeTag.textContent = 'Availability lookup failed';
            availabilityModeTag.className = 'availability-tag muted';
            console.error(error);
        }
    }

    prevWeekBtn.addEventListener('click', function () {
        const newWeek = new Date(currentWeekStart);
        newWeek.setDate(newWeek.getDate() - 7);

        if (newWeek < startOfWeek(today)) {
            currentWeekStart = startOfWeek(today);
        } else {
            currentWeekStart = newWeek;
        }

        renderWeekStrip();
    });

    nextWeekBtn.addEventListener('click', function () {
        const newWeek = new Date(currentWeekStart);
        newWeek.setDate(newWeek.getDate() + 7);
        currentWeekStart = newWeek;
        renderWeekStrip();
    });

    updateSummaryLabels();
    renderWeekStrip();

    if (confirmAppointmentDate.value && confirmDentistId.value) {
        loadAvailableSlots();
    } else {
        confirmSlotFeedback.textContent = 'Select a dentist and date to load available time slots.';
    }
});
</script>
@endif
@endsection
