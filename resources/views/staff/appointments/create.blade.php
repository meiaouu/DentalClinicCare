@extends('staff.layouts.app')

@section('content')
<style>
    .create-appointment-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 980px;
    }

    .create-appointment-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    .create-appointment-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .create-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
    }

    .alert-box {
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid transparent;
    }

    .alert-danger {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 18px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #ffffff;
        color: #0f172a;
        box-sizing: border-box;
        outline: none;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #0f9d8a;
        box-shadow: 0 0 0 3px rgba(15, 157, 138, 0.10);
    }

    .form-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .helper-text {
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }

    .slot-box {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px;
    }

    .slot-feedback {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 10px;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .slot-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        border-radius: 10px;
        min-height: 42px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .slot-btn:hover {
        border-color: #0f9d8a;
        color: #0f9d8a;
    }

    .slot-btn.active {
        background: #0f9d8a;
        color: #ffffff;
        border-color: #0f9d8a;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #0f9d8a;
        color: #ffffff;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #334155;
    }

    @media (max-width: 900px) {
        .slot-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: auto;
        }

        .slot-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 500px) {
        .slot-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="create-appointment-page">
    <div class="create-appointment-header">
        <h1>Create Appointment</h1>
        <p>Use this when a patient calls, messages, or emails the clinic and staff needs to encode the appointment directly.</p>
    </div>

    @if($errors->any())
        <div class="alert-box alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="create-card">
        <form method="POST" action="{{ route('staff.appointments.store') }}" id="staffCreateAppointmentForm">
            @csrf

            <input type="hidden" name="start_time" id="selected_start_time" value="{{ old('start_time') }}">

            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label" for="patient_id">Patient</label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->patient_id }}" @selected(old('patient_id') == $patient->patient_id)>
                                {{ trim(($patient->last_name ?? '') . ', ' . ($patient->first_name ?? '') . ' ' . ($patient->middle_name ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="helper-text">Select an existing patient record first.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="service_id">Service</label>
                    <select name="service_id" id="service_id" class="form-select" required>
                        <option value="">Select service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->service_id }}" @selected(old('service_id') == $service->service_id)>
                                {{ $service->service_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="dentist_id">Dentist</label>
                    <select name="dentist_id" id="dentist_id" class="form-select" required>
                        <option value="">Select dentist</option>
                        @foreach($dentists as $dentist)
                            <option value="{{ $dentist->dentist_id }}" @selected(old('dentist_id') == $dentist->dentist_id)>
                                {{ trim(($dentist->user?->last_name ?? '') . ', ' . ($dentist->user?->first_name ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="appointment_date">Appointment Date</label>
                    <input
                        type="date"
                        name="appointment_date"
                        id="appointment_date"
                        value="{{ old('appointment_date', now()->toDateString()) }}"
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Selected Time</label>
                    <input
                        type="text"
                        id="selected_time_display"
                        class="form-input"
                        value="{{ old('start_time') }}"
                        placeholder="Select an available time below"
                        readonly
                    >
                </div>

                <div class="form-group full">
                    <label class="form-label">Available Time Slots</label>
                    <div class="slot-box">
                        <div class="slot-feedback" id="slot_feedback">
                            Select service, dentist, and date to load available time slots.
                        </div>
                        <div class="slot-grid" id="slot_grid"></div>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea
                        name="remarks"
                        id="remarks"
                        class="form-textarea"
                        placeholder="Phone booking, email request, special instruction, or note from staff."
                    >{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Appointment</button>
                <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const dentistSelect = document.getElementById('dentist_id');
    const dateInput = document.getElementById('appointment_date');
    const slotGrid = document.getElementById('slot_grid');
    const slotFeedback = document.getElementById('slot_feedback');
    const selectedStartTime = document.getElementById('selected_start_time');
    const selectedTimeDisplay = document.getElementById('selected_time_display');

    function formatTime12Hour(time24) {
        if (!time24) return '';
        const [hour, minute] = time24.split(':');
        const h = parseInt(hour, 10);
        const suffix = h >= 12 ? 'PM' : 'AM';
        const displayHour = ((h + 11) % 12 + 1).toString().padStart(2, '0');
        return `${displayHour}:${minute} ${suffix}`;
    }

    function clearSlots(message) {
        slotGrid.innerHTML = '';
        slotFeedback.textContent = message;
        selectedStartTime.value = '';
        selectedTimeDisplay.value = '';
    }

    function markSelectedButton() {
        const buttons = slotGrid.querySelectorAll('.slot-btn');
        buttons.forEach((button) => {
            button.classList.toggle('active', button.dataset.time === selectedStartTime.value);
        });
    }

    async function loadSlots() {
        const serviceId = serviceSelect.value;
        const dentistId = dentistSelect.value;
        const date = dateInput.value;

        if (!serviceId || !dentistId || !date) {
            clearSlots('Select service, dentist, and date to load available time slots.');
            return;
        }

        clearSlots('Loading available time slots...');

        try {
            const url = `{{ route('staff.appointments.available-slots') }}?service_id=${encodeURIComponent(serviceId)}&dentist_id=${encodeURIComponent(dentistId)}&date=${encodeURIComponent(date)}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to load available slots. Status: ${response.status}`);
            }

            const result = await response.json();
            const slots = Array.isArray(result.available_slots) ? result.available_slots : [];

            slotGrid.innerHTML = '';

            if (slots.length === 0) {
                clearSlots('No available time slots for the selected date.');
                return;
            }

            slotFeedback.textContent = `${slots.length} available time slot(s) found.`;

            slots.forEach((slot) => {
                const startTime = (slot.start_time || '').substring(0, 5);

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'slot-btn';
                button.dataset.time = startTime;
                button.textContent = formatTime12Hour(startTime);

                button.addEventListener('click', function () {
                    selectedStartTime.value = startTime;
                    selectedTimeDisplay.value = formatTime12Hour(startTime);
                    markSelectedButton();
                });

                slotGrid.appendChild(button);
            });

            if (selectedStartTime.value) {
                const stillExists = slots.some((slot) => (slot.start_time || '').substring(0, 5) === selectedStartTime.value);

                if (!stillExists) {
                    selectedStartTime.value = '';
                    selectedTimeDisplay.value = '';
                }
            }

            markSelectedButton();
        } catch (error) {
            clearSlots('Unable to load available time slots.');
            console.error(error);
        }
    }

    serviceSelect.addEventListener('change', loadSlots);
    dentistSelect.addEventListener('change', loadSlots);
    dateInput.addEventListener('change', loadSlots);

    if (serviceSelect.value && dentistSelect.value && dateInput.value) {
        loadSlots();

        if (selectedStartTime.value) {
            selectedTimeDisplay.value = formatTime12Hour(selectedStartTime.value);
        }
    }
});
</script>
@endsection
