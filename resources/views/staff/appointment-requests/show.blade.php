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
@endphp

<style>
    .request-page{padding:32px;max-width:1320px;margin:0 auto;background:#f8fafc}
    .request-header{margin-bottom:24px}
    .request-title{font-size:28px;font-weight:800;color:#0f172a;margin:0 0 8px}
    .request-subtitle{color:#64748b;margin:0;font-size:14px}
    .alert-box{padding:14px 16px;border-radius:14px;margin-bottom:20px;border:1px solid transparent}
    .alert-success{background:#ecfdf5;color:#166534;border-color:#bbf7d0}
    .alert-danger{background:#fef2f2;color:#991b1b;border-color:#fecaca}
    .request-grid{display:grid;grid-template-columns:2fr 1.15fr;gap:24px;align-items:start}
    .panel{background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:22px;box-shadow:0 10px 25px rgba(15,23,42,.04)}
    .panel+.panel{margin-top:20px}
    .panel-title{font-size:18px;font-weight:800;color:#0f172a;margin:0 0 16px;padding-bottom:12px;border-bottom:1px solid #eef2f7}
    .info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .info-item{border:1px solid #edf2f7;border-radius:14px;background:#f8fafc;padding:14px}
    .info-label{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px}
    .info-value{font-size:14px;font-weight:600;color:#0f172a;line-height:1.6;word-break:break-word}
    .summary-list{display:grid;gap:10px}
    .summary-row{display:grid;grid-template-columns:180px 1fr;gap:12px;padding:10px 0;border-bottom:1px solid #eef2f7}
    .summary-row:last-child{border-bottom:none}
    .summary-key{font-size:13px;font-weight:700;color:#64748b}
    .summary-value{font-size:14px;font-weight:600;color:#0f172a}
    .status-badge{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border-radius:999px;font-size:12px;font-weight:700;background:#e0f2fe;color:#075985}
    .answer-list{margin:0;padding-left:18px}
    .answer-list li{margin-bottom:8px;color:#334155;line-height:1.6}
    .form-group{margin-bottom:14px}
    .form-label{display:block;font-size:13px;font-weight:700;color:#334155;margin-bottom:8px}
    .form-control,.form-select,textarea{width:100%;border:1px solid #cbd5e1;border-radius:12px;padding:12px 14px;font-size:14px;background:#fff;box-sizing:border-box}
    textarea{min-height:96px;resize:vertical}
    .btn{width:100%;border:none;border-radius:12px;padding:12px 16px;color:#fff;font-weight:700;cursor:pointer}
    .btn-success{background:#16a34a}
    .btn-warning{background:#f59e0b}
    .btn-danger{background:#dc2626}
    .btn-secondary{background:#334155}
    .btn-light{background:#e2e8f0;color:#0f172a}
    .converted-box{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:14px 16px;border-radius:14px;margin-bottom:20px;line-height:1.6}
    .inline-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
    .inline-btn{border:none;border-radius:10px;padding:8px 12px;font-size:12px;font-weight:700;cursor:pointer}
    .inline-btn-edit{background:#e2e8f0;color:#0f172a}
    .inline-btn-reset{background:#fef3c7;color:#92400e}
    .edit-block{display:none;margin-top:12px;padding:14px;border:1px dashed #cbd5e1;border-radius:12px;background:#f8fafc}
    .edit-block.active{display:block}
    .calendar-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px}
    .calendar-month-label{margin:0;font-size:16px;font-weight:800;color:#0f172a}
    .calendar-btn{width:38px;height:38px;border-radius:10px;border:1px solid #d7e0e8;background:#fff;color:#334155;font-weight:800}
    .calendar-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-bottom:10px;text-align:center;font-size:12px;font-weight:800;color:#64748b}
    #staffCalendarGrid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
    #staffCalendarGrid button{min-height:42px;border-radius:10px;font-weight:700}
    .slot-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
    .time-slot-btn{border:1px solid #dbe4ea;background:#fff;border-radius:12px;min-height:44px;padding:10px;font-weight:700;text-align:center;color:#334155;cursor:pointer;transition:.2s ease}
    .time-slot-btn:hover:not(.disabled){border-color:#0f9d8a;color:#0f9d8a}
    .time-slot-btn.active{background:#0f9d8a;border-color:#0f9d8a;color:#fff}
    .time-slot-btn.disabled{background:#f8fafc;color:#94a3b8;border-color:#e2e8f0;cursor:not-allowed}
    .helper-text{font-size:13px;color:#64748b;line-height:1.6}
    @media (max-width:991px){
        .request-grid{grid-template-columns:1fr}
        .info-grid{grid-template-columns:1fr}
        .summary-row{grid-template-columns:1fr}
    }
</style>

<div class="request-page">
    <div class="request-header">
        <h1 class="request-title">Review Appointment Request</h1>
        <p class="request-subtitle">Check request details, validate the schedule, then confirm, reschedule, or reject.</p>
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
                    <div class="summary-row"><div class="summary-key">Request Code</div><div class="summary-value">{{ $requestItem->request_code }}</div></div>
                    <div class="summary-row"><div class="summary-key">Status</div><div class="summary-value"><span class="status-badge">{{ ucfirst(str_replace('_', ' ', $requestItem->request_status)) }}</span></div></div>
                    <div class="summary-row"><div class="summary-key">Patient</div><div class="summary-value">{{ $displayPatientName ?: '—' }}</div></div>
                    <div class="summary-row"><div class="summary-key">Contact Number</div><div class="summary-value">{{ $displayContact }}</div></div>
                    <div class="summary-row"><div class="summary-key">Email</div><div class="summary-value">{{ $displayEmail }}</div></div>
                    <div class="summary-row"><div class="summary-key">Service</div><div class="summary-value">{{ $requestItem->service?->service_name ?? '—' }}</div></div>
                    <div class="summary-row"><div class="summary-key">Requested Date</div><div class="summary-value">{{ $requestedDate ?: '—' }}</div></div>
                    <div class="summary-row"><div class="summary-key">Requested Time</div><div class="summary-value">{{ $requestedTime ?: '—' }}</div></div>
                    <div class="summary-row"><div class="summary-key">Preferred Dentist</div><div class="summary-value">{{ $displayPreferredDentist }}</div></div>
                    <div class="summary-row"><div class="summary-key">Patient Concerns</div><div class="summary-value">{{ $notesOrConcerns ?: '—' }}</div></div>
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
                    <p class="helper-text">
                        Approve the request as-is, or use the edit buttons to adjust dentist, date, or time before confirmation.
                    </p>

                    <form method="POST" action="{{ route('staff.appointment-requests.confirm', $requestItem->request_id) }}" id="confirmAppointmentForm">
                        @csrf

                        <input type="hidden" name="dentist_id" id="confirm_dentist_id" value="{{ $oldConfirmDentistId }}">
                        <input type="hidden" name="appointment_date" id="confirm_appointment_date" value="{{ $oldConfirmDate }}">
                        <input type="hidden" name="start_time" id="confirm_start_time" value="{{ $oldConfirmTime }}">

                        <div class="summary-list" style="margin-bottom:14px;">
                            <div class="summary-row">
                                <div class="summary-key">Approved Dentist</div>
                                <div class="summary-value" id="approvedDentistLabel">{{ $displayPreferredDentist }}</div>
                            </div>
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
                            <button type="button" class="inline-btn inline-btn-edit" data-edit-target="dentistEditBlock">Edit Dentist</button>
                            <button type="button" class="inline-btn inline-btn-edit" data-edit-target="dateEditBlock">Edit Date</button>
                            <button type="button" class="inline-btn inline-btn-edit" data-edit-target="timeEditBlock">Edit Time</button>
                            <button type="button" class="inline-btn inline-btn-reset" id="resetConfirmValuesBtn">Reset to Requested</button>
                        </div>

                        <div class="edit-block" id="dentistEditBlock">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Assign Dentist</label>
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
                        </div>

                        <div class="edit-block" id="dateEditBlock">
                            <div class="calendar-toolbar">
                                <button type="button" class="calendar-btn" id="prevMonthBtn">&lt;</button>
                                <h6 id="calendarMonthLabel" class="calendar-month-label">Select Date</h6>
                                <button type="button" class="calendar-btn" id="nextMonthBtn">&gt;</button>
                            </div>
                            <div class="calendar-weekdays">
                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="staffCalendarGrid"></div>
                            <div class="helper-text" style="margin-top:12px;">
                                Selected date: <strong id="calendarSelectedDateText">{{ $oldConfirmDate ?: 'None' }}</strong>
                            </div>
                        </div>

                        <div class="edit-block" id="timeEditBlock">
                            <div class="form-group">
                                <label class="form-label">Available Time Slots</label>
                                <div id="confirmTimeSlotGrid" class="slot-grid"></div>
                                <div id="confirmSlotFeedback" class="helper-text" style="margin-top:10px;">
                                    Select a date to load available time slots.
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
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
                                        @selected((string) $oldConfirmDentistId === (string) $dentist->dentist_id)
                                    >
                                        {{ $dentist->user?->full_name ?? ('Dentist #' . $dentist->dentist_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Appointment Date</label>
                            <input type="date" name="appointment_date" class="form-control" value="{{ $oldConfirmDate }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="{{ $oldConfirmTime }}" required>
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

    const confirmDentistSelect = document.getElementById('confirmDentistSelect');
    const confirmTimeSlotGrid = document.getElementById('confirmTimeSlotGrid');
    const confirmSlotFeedback = document.getElementById('confirmSlotFeedback');

    const calendarGrid = document.getElementById('staffCalendarGrid');
    const calendarMonthLabel = document.getElementById('calendarMonthLabel');
    const calendarSelectedDateText = document.getElementById('calendarSelectedDateText');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');

    const toggleButtons = document.querySelectorAll('[data-edit-target]');
    const resetConfirmValuesBtn = document.getElementById('resetConfirmValuesBtn');

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let currentMonth = confirmAppointmentDate.value
        ? new Date(confirmAppointmentDate.value + 'T00:00:00')
        : new Date(today.getFullYear(), today.getMonth(), 1);

    currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);

    let selectedDate = confirmAppointmentDate.value
        ? new Date(confirmAppointmentDate.value + 'T00:00:00')
        : null;

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
        } else {
            approvedDentistLabel.textContent = requestDefaults.dentistLabel || 'Clinic will assign';
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
        currentMonth = selectedDate
            ? new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1)
            : new Date(today.getFullYear(), today.getMonth(), 1);

        renderCalendar();
        updateSummaryLabels();
        loadAvailableSlots();
    });

    async function loadAvailableSlots() {
        confirmTimeSlotGrid.innerHTML = '';

        if (!requestDefaults.serviceId || !confirmAppointmentDate.value) {
            confirmSlotFeedback.textContent = 'Select a date to load available time slots.';
            return;
        }

        let url = `{{ route('booking.available.slots') }}?service_id=${encodeURIComponent(requestDefaults.serviceId)}&date=${encodeURIComponent(confirmAppointmentDate.value)}`;
        if (confirmDentistId.value) {
            url += `&dentist_id=${encodeURIComponent(confirmDentistId.value)}`;
        }

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

            if (clinicHours.length === 0) {
                confirmSlotFeedback.textContent = 'No clinic hours configured for this date.';
                return;
            }

            const availableMap = {};
            slots.forEach((slot) => {
                availableMap[normalizeTime(slot.start_time)] = slot;
            });

            clinicHours.forEach((hour) => {
                const normalizedHour = normalizeTime(hour);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.textContent = formatHourLabel(normalizedHour);

                if (availableMap[normalizedHour]) {
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
            console.error(error);
        }
    }

    function renderCalendar() {
        calendarGrid.innerHTML = '';

        const year = currentMonth.getFullYear();
        const month = currentMonth.getMonth();

        calendarMonthLabel.textContent = currentMonth.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startWeekday = firstDay.getDay();
        const totalDays = lastDay.getDate();

        for (let i = 0; i < startWeekday; i++) {
            const blank = document.createElement('div');
            calendarGrid.appendChild(blank);
        }

        for (let day = 1; day <= totalDays; day++) {
            const cellDate = new Date(year, month, day);
            cellDate.setHours(0, 0, 0, 0);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn-light';
            btn.textContent = day;

            const isPast = cellDate < today;
            const isSelected = selectedDate && formatDateLocal(cellDate) === formatDateLocal(selectedDate);

            if (isPast) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            } else if (isSelected) {
                btn.className = 'btn btn-secondary';
            } else {
                btn.className = 'btn-light';
            }

            btn.addEventListener('click', function () {
                selectedDate = cellDate;
                const formatted = formatDateLocal(cellDate);

                confirmAppointmentDate.value = formatted;
                updateSummaryLabels();
                renderCalendar();
                loadAvailableSlots();
            });

            calendarGrid.appendChild(btn);
        }
    }

    prevMonthBtn.addEventListener('click', function () {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function () {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        renderCalendar();
    });

    updateSummaryLabels();
    renderCalendar();

    if (confirmAppointmentDate.value) {
        loadAvailableSlots();
    }
});
</script>
@endif
@endsection
