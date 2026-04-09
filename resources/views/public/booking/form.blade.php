@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 40px 16px 72px;
    }

    .booking-container {
        max-width: 1040px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .booking-title {
        margin: 0 0 8px;
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        color: #0f172a;
    }

    .booking-subtitle {
        margin: 0 auto;
        max-width: 720px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
    }

    .booking-stepper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .booking-step {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .booking-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        border: 2px solid #dbe4ea;
        background: #fff;
        color: #64748b;
    }

    .booking-step-circle.active {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #fff;
    }

    .booking-step-line {
        width: 64px;
        height: 2px;
        background: #dbe4ea;
        border-radius: 999px;
    }

    .booking-step-label {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
    }

    .booking-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .booking-section {
        border: 1px solid #e9eef3;
        border-radius: 18px;
        padding: 20px;
        background: #fff;
    }

    .booking-section + .booking-section {
        margin-top: 20px;
    }

    .booking-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eef2f6;
    }

    .booking-section-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .booking-section-note {
        font-size: 12px;
        font-weight: 700;
        color: #0f766e;
        background: #ecfdf5;
        border-radius: 999px;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        height: 50px;
        border-radius: 12px;
        border: 1px solid #d7e0e8;
        font-size: 15px;
        box-shadow: none !important;
    }

    textarea.form-control {
        min-height: 120px;
        height: auto;
        padding-top: 12px;
        resize: vertical;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0f9d8a;
        box-shadow: 0 0 0 4px rgba(15, 157, 138, 0.10) !important;
    }

    .booking-panel {
        border: 1px solid #e6edf2;
        border-radius: 16px;
        padding: 16px;
        background: #f8fafc;
        height: 30%;
    }
    .booking-calendaer-panel {
        border: 1px solid #e6edf2;
        border-radius: 16px;
        padding: 16px;
        background: #f8fafc;
        height: 50%;
    }
    .booking-slot-panel {
        border: 1px solid #e6edf2;
        border-radius: 16px;
        padding: 16px;
        background: #f8fafc;
        height: 50%;
    }

    .booking-panel-title {
        margin: 0 0 8px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .booking-panel-text {
        font-size: 14px;
        line-height: 1.7;
        color: #475569;
    }

    .doctor-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .doctor-card {
        border: 1px solid #dbe4ea;
        background: #fff;
        border-radius: 16px;
        padding: 14px;
        cursor: pointer;
        transition: 0.2s ease;
        text-align: left;
    }

    .doctor-card:hover {
        border-color: #0f9d8a;
        background: #f0fdfa;
    }

    .doctor-card.active {
        border-color: #0f9d8a;
        background: #ecfdf5;
        box-shadow: inset 0 0 0 1px #0f9d8a;
    }

    .doctor-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .doctor-card-subtitle {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }

    .doctor-any-card {
        margin-bottom: 12px;
    }

    .calendar-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        gap: 10px;
    }

    .calendar-month-label {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .calendar-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid #d7e0e8;
        background: #fff;
        color: #334155;
        font-weight: 800;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 10px;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
    }

    #bookingCalendarGrid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    #bookingCalendarGrid button {
        min-height: 44px;
        border-radius: 10px;
        font-weight: 700;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .time-slot-btn {
        border: 1px solid #dbe4ea;
        background: #fff;
        border-radius: 12px;
        min-height: 46px;
        padding: 10px;
        font-weight: 700;
        text-align: center;
        color: #334155;
        cursor: pointer;
        transition: 0.2s ease;
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
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .booking-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .booking-primary-btn {
        min-width: 220px;
        height: 52px;
        border: none;
        border-radius: 14px;
        background: #0f9d8a;
        color: #fff;
        font-size: 15px;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .booking-primary-btn:hover {
        background: #0d8574;
    }

    .alert {
        border-radius: 16px;
    }

    .form-check {
        padding: 10px 12px 10px 34px;
        border: 1px solid #e5edf2;
        border-radius: 12px;
        background: #fff;
        margin-bottom: 8px;
    }

    @media (max-width: 991.98px) {
        .slot-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .booking-page {
            padding: 28px 12px 56px;
        }

        .booking-card {
            padding: 18px;
        }

        .booking-section {
            padding: 16px;
        }

        .booking-step-line {
            width: 40px;
        }

        .doctor-grid,
        .slot-grid {
            grid-template-columns: 1fr;
        }

        .booking-actions {
            justify-content: stretch;
        }

        .booking-primary-btn {
            width: 100%;
        }
    }
</style>

<div class="booking-page">
    <div class="booking-container">
        <div class="booking-header">
            <h1 class="booking-title">{{ $isGuest ? 'Guest Booking Form' : 'Book Appointment' }}</h1>
            <p class="booking-subtitle">
                Fill in your information, choose a dental service, select an available doctor, and pick your preferred date and time.
            </p>

            <div class="booking-stepper">
                <div class="booking-step">
                    <span class="booking-step-circle active">1</span>
                    <span class="booking-step-label">Fill Up</span>
                </div>
                <span class="booking-step-line"></span>
                <div class="booking-step">
                    <span class="booking-step-circle">2</span>
                    <span class="booking-step-label">Review</span>
                </div>
                <span class="booking-step-line"></span>
                <div class="booking-step">
                    <span class="booking-step-circle">3</span>
                    <span class="booking-step-label">Submitted</span>
                </div>
            </div>
        </div>

        <div class="booking-card">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('booking.review') }}" id="bookingForm">
                @csrf

                <div class="booking-section">
                    <div class="booking-section-head">
                        <h5 class="booking-section-title">Patient Information</h5>
                        <span class="booking-section-note">{{ $isGuest ? 'Guest Details' : 'Patient Details' }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                value="{{ old('first_name', $patient->first_name ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control"
                                value="{{ old('middle_name', $patient->middle_name ?? '') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                value="{{ old('last_name', $patient->last_name ?? '') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('sex', $patient->sex ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $patient->sex ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Birth Date</label>
                            <input type="date" name="birth_date" class="form-control"
                                value="{{ old('birth_date', $patient->birth_date ?? '') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status" class="form-select" required>
                                <option value="">Select Civil Status</option>
                                @php $civilStatus = old('civil_status', $patient->civil_status ?? ''); @endphp
                                <option value="single" {{ $civilStatus === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ $civilStatus === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ $civilStatus === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ $civilStatus === 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control"
                                value="{{ old('occupation', $patient->occupation ?? '') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control"
                                value="{{ old('contact_number', $prefillContact ?? $patient->contact_number ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $patient->email ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control"
                                value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
                        </div>

                        <div class="col-md-4 mb-0">
                            <label class="form-label">Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_number" class="form-control"
                                placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX"
                                value="{{ old('emergency_contact_number', $patient->emergency_contact_number ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <div class="booking-section-head">
                        <h5 class="booking-section-title">Address</h5>
                        <span class="booking-section-note">Residential Information</span>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Region</label>
                            <select name="region" id="region" class="form-select" required>
                                <option value="">Select Region</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Province</label>
                            <select name="province" id="province" class="form-select" required disabled>
                                <option value="">Select Province</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">City / Municipality</label>
                            <select name="city" id="city" class="form-select" required disabled>
                                <option value="">Select City / Municipality</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Barangay</label>
                            <select name="barangay" id="barangay" class="form-select" required disabled>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-0">
                            <label class="form-label">Street / House No. / Unit</label>
                            <input type="text" name="address_line" class="form-control"
                                value="{{ old('address_line') }}"
                                placeholder="Street / House No. / Unit">
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <div class="booking-section-head">
                        <h5 class="booking-section-title">Appointment Details</h5>
                        <span class="booking-section-note">Service and Schedule</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Dental Service</label>
                            <select name="service_id" id="service_id" class="form-select" required>
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->service_id }}"
                                        data-description="{{ $service->description }}"
                                        data-duration="{{ $service->estimated_duration_minutes }}"
                                        data-price="{{ $service->estimated_price }}"
                                        {{ (string) old('service_id') === (string) $service->service_id ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="booking-panel">
                                <h6 class="booking-panel-title">Service Information</h6>
                                <div id="serviceMetaDescription" class="booking-panel-text">
                                    Select a service to view the description.
                                </div>
                                <div class="mt-3 booking-panel-text">
                                    <strong>Estimated Duration:</strong> <span id="serviceMetaDuration">-</span><br>
                                    <strong>Estimated Price:</strong> ₱<span id="serviceMetaPrice">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Available Doctor</label>
                            <input type="hidden" name="preferred_dentist_id" id="preferred_dentist_id"
                                   value="{{ old('preferred_dentist_id') }}">

                            <div class="doctor-any-card">
                                <button type="button"
                                        class="doctor-card w-100 {{ old('preferred_dentist_id') ? '' : 'active' }}"
                                        data-dentist-value="">
                                    <div class="doctor-card-title">Any Available Dentist</div>
                                    <div class="doctor-card-subtitle">Let the clinic assign an available doctor for your selected service and time.</div>
                                </button>
                            </div>

                            <div class="doctor-grid" id="doctorCardGrid">
                                @forelse(($dentists ?? []) as $dentist)
                                    <button type="button"
                                            class="doctor-card {{ (string) old('preferred_dentist_id') === (string) $dentist->dentist_id ? 'active' : '' }}"
                                            data-dentist-value="{{ $dentist->dentist_id }}">
                                        <div class="doctor-card-title">Dentist #{{ $dentist->dentist_id }}</div>
                                        <div class="doctor-card-subtitle">Select this doctor for your preferred appointment request.</div>
                                    </button>
                                @empty
                                    <div class="booking-panel">
                                        <div class="booking-panel-text">No available dentists found.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Service-Specific Questions</label>
                            <div id="dynamicQuestions" class="booking-panel">
                                <p class="text-muted mb-0">Select a service to load additional questions.</p>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <input type="hidden" name="preferred_date" id="preferred_date"
                                   value="{{ old('preferred_date') }}"
                                   min="{{ now()->toDateString() }}">

                            <div class="booking-calendaer-panel">
                                <div class="calendar-toolbar">
                                    <button type="button" class="calendar-btn" id="prevMonthBtn">&lt;</button>
                                    <h6 id="calendarMonthLabel" class="calendar-month-label">Select Date</h6>
                                    <button type="button" class="calendar-btn" id="nextMonthBtn">&gt;</button>
                                </div>

                                <div class="calendar-weekdays">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>

                                <div id="bookingCalendarGrid"></div>

                                <div class="mt-3 booking-panel-text">
                                    <strong>Selected Date:</strong>
                                    <span id="selectedDateLabel">{{ old('preferred_date') ?: 'None' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="booking-slot-panel h-100">
                                <label class="form-label">Available Time Slots</label>
                                <input type="hidden" name="preferred_start_time" id="preferred_start_time" value="{{ old('preferred_start_time') }}">
                                <div id="timeSlotGrid" class="slot-grid"></div>
                                <div id="slotFeedback" class="small text-muted mt-3">
                                    Select a service and date to load available times.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-0">
                            <label class="form-label">Notes / Concerns</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="booking-actions">
                    <button type="submit" class="booking-primary-btn">Review Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div
    id="addressOldValues"
    data-region="{{ old('region', '') }}"
    data-province="{{ old('province', '') }}"
    data-city="{{ old('city', '') }}"
    data-barangay="{{ old('barangay', '') }}"
    hidden
></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const dentistInput = document.getElementById('preferred_dentist_id');
    const dentistCards = document.querySelectorAll('[data-dentist-value]');
    const dateInput = document.getElementById('preferred_date');
    const dynamicQuestions = document.getElementById('dynamicQuestions');

    const descriptionEl = document.getElementById('serviceMetaDescription');
    const durationEl = document.getElementById('serviceMetaDuration');
    const priceEl = document.getElementById('serviceMetaPrice');

    const calendarGrid = document.getElementById('bookingCalendarGrid');
    const calendarMonthLabel = document.getElementById('calendarMonthLabel');
    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const slotFeedback = document.getElementById('slotFeedback');

    const hiddenTimeInput = document.getElementById('preferred_start_time');
    const timeSlotGrid = document.getElementById('timeSlotGrid');

    const regionEl = document.getElementById('region');
    const provinceEl = document.getElementById('province');
    const cityEl = document.getElementById('city');
    const barangayEl = document.getElementById('barangay');

    const oldValuesEl = document.getElementById('addressOldValues');

    const oldRegion = oldValuesEl?.dataset.region || '';
    const oldProvince = oldValuesEl?.dataset.province || '';
    const oldCity = oldValuesEl?.dataset.city || '';
    const oldBarangay = oldValuesEl?.dataset.barangay || '';

    const addressData = {
        "NCR": {
            "Metro Manila": {
                "Quezon City": ["Bagumbayan", "Commonwealth", "Batasan Hills"],
                "Manila": ["Ermita", "Malate", "Sampaloc"]
            }
        },
        "Region III": {
            "Bulacan": {
                "Malolos": ["Santo Rosario", "Longos", "Tikay"],
                "San Jose del Monte": ["Minuyan", "Muzon", "Tungkong Mangga"]
            },
            "Pampanga": {
                "Angeles City": ["Balibago", "Pulung Maragul", "Anunas"],
                "San Fernando": ["Del Pilar", "Juliana", "Sto. Niño"]
            }
        }
    };

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    let selectedDate = dateInput.value ? new Date(dateInput.value + 'T00:00:00') : null;

    function formatDateLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function normalizeTime(value) {
        if (!value) return '';
        return value.length === 5 ? value + ':00' : value;
    }

    function updateServiceMetaFromOption() {
        const option = serviceSelect.options[serviceSelect.selectedIndex];

        if (!option || !option.value) {
            descriptionEl.textContent = 'Select a service to view the description.';
            durationEl.textContent = '-';
            priceEl.textContent = '-';
            return;
        }

        descriptionEl.textContent = option.dataset.description || '-';
        durationEl.textContent = (option.dataset.duration || '-') + ' minutes';
        priceEl.textContent = option.dataset.price || '-';
    }

    async function loadServiceQuestions() {
        const serviceId = serviceSelect.value;

        if (!serviceId) {
            dynamicQuestions.innerHTML = '<p class="text-muted mb-0">Select a service to load additional questions.</p>';
            return;
        }

        try {
            const response = await fetch(`/booking/services/${serviceId}/questions`);
            const questions = await response.json();

            if (!Array.isArray(questions) || questions.length === 0) {
                dynamicQuestions.innerHTML = '<p class="text-muted mb-0">No additional questions for this service.</p>';
                return;
            }

            let html = '';

            questions.forEach((question) => {
                html += `<div class="mb-3">`;
                html += `<label class="form-label"><strong>${question.option_name}</strong>${question.is_required ? ' *' : ''}</label>`;

                const values = Array.isArray(question.values) ? question.values : [];
                const fieldName = `answers[${question.option_id}]`;

                if (['select', 'radio'].includes(question.option_type) && values.length > 0) {
                    html += `<select name="${fieldName}" class="form-select">`;
                    html += `<option value="">Select</option>`;
                    values.forEach((value) => {
                        html += `<option value="${value.value_id}">${value.value_label}</option>`;
                    });
                    html += `</select>`;
                } else if (question.option_type === 'checkbox' && values.length > 0) {
                    values.forEach((value) => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="${fieldName}[]" value="${value.value_id}" id="opt_${question.option_id}_${value.value_id}">
                                <label class="form-check-label" for="opt_${question.option_id}_${value.value_id}">
                                    ${value.value_label}
                                </label>
                            </div>
                        `;
                    });
                } else if (question.option_type === 'textarea') {
                    html += `<textarea name="${fieldName}" class="form-control" rows="3"></textarea>`;
                } else if (question.option_type === 'number') {
                    html += `<input type="number" name="${fieldName}" class="form-control">`;
                } else {
                    html += `<input type="text" name="${fieldName}" class="form-control">`;
                }

                html += `</div>`;
            });

            dynamicQuestions.innerHTML = html;
        } catch (error) {
            dynamicQuestions.innerHTML = '<p class="text-danger mb-0">Failed to load service questions.</p>';
        }
    }

    async function loadAvailableSlots() {
        const serviceId = serviceSelect.value;
        const dentistId = dentistInput.value;
        const date = dateInput.value;

        hiddenTimeInput.value = '';
        timeSlotGrid.innerHTML = '';

        if (!serviceId || !date) {
            slotFeedback.textContent = 'Select a service and date to load available times.';
            return;
        }

        let url = `{{ route('booking.available.slots') }}?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;
        if (dentistId) {
            url += `&dentist_id=${encodeURIComponent(dentistId)}`;
        }

        try {
            slotFeedback.textContent = 'Loading available time slots...';

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Failed to load slots');
            }

            const result = await response.json();
            const slots = Array.isArray(result.available_slots) ? result.available_slots : [];
            const clinicHours = Array.isArray(result.clinic_hours) ? result.clinic_hours : [];

            if (clinicHours.length === 0) {
                slotFeedback.textContent = 'No clinic hours configured.';
                timeSlotGrid.innerHTML = '<div class="text-danger">No clinic hours available.</div>';
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

                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.time-slot-btn').forEach(el => {
                            el.classList.remove('active');
                        });

                        btn.classList.add('active');
                        hiddenTimeInput.value = normalizedHour.slice(0, 5);
                    });
                } else {
                    btn.classList.add('disabled');
                    btn.disabled = true;
                }

                timeSlotGrid.appendChild(btn);
            });

            slotFeedback.textContent = slots.length > 0
                ? `${slots.length} available time slot(s) found.`
                : 'No available slots for the selected date.';
        } catch (error) {
            console.error(error);
            timeSlotGrid.innerHTML = '<div class="text-danger">Failed to load slots</div>';
            slotFeedback.textContent = 'Failed to load available slots.';
        }
    }

    function formatHourLabel(time24) {
        const [hour, minute] = time24.split(':');
        const h = parseInt(hour, 10);
        const suffix = h >= 12 ? 'PM' : 'AM';
        const displayHour = ((h + 11) % 12 + 1).toString().padStart(2, '0');
        return `${suffix} ${displayHour}:${minute}`;
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
            btn.className = 'btn btn-sm';
            btn.textContent = day;

            const isPast = cellDate < today;
            const isSelected = selectedDate && formatDateLocal(cellDate) === formatDateLocal(selectedDate);

            if (isPast) {
                btn.classList.add('btn-light');
                btn.disabled = true;
                btn.style.opacity = '0.5';
            } else if (isSelected) {
                btn.classList.add('btn-primary');
            } else {
                btn.classList.add('btn-outline-secondary');
            }

            btn.addEventListener('click', function () {
                selectedDate = cellDate;
                const formatted = formatDateLocal(cellDate);

                dateInput.value = formatted;
                selectedDateLabel.textContent = formatted;

                renderCalendar();
                loadAvailableSlots();
            });

            calendarGrid.appendChild(btn);
        }
    }

    function resetSelect(selectEl, placeholder) {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        selectEl.disabled = true;
    }

    function populateSelect(selectEl, items, placeholder, selectedValue = '') {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;

            if (selectedValue && selectedValue === item) {
                option.selected = true;
            }

            selectEl.appendChild(option);
        });

        selectEl.disabled = items.length === 0;
    }

    function loadRegions() {
        populateSelect(regionEl, Object.keys(addressData), 'Select Region', oldRegion);
    }

    function loadProvinces(region, selectedProvince = '') {
        resetSelect(cityEl, 'Select City / Municipality');
        resetSelect(barangayEl, 'Select Barangay');

        if (!region || !addressData[region]) {
            resetSelect(provinceEl, 'Select Province');
            return;
        }

        populateSelect(provinceEl, Object.keys(addressData[region]), 'Select Province', selectedProvince);
    }

    function loadCities(region, province, selectedCity = '') {
        resetSelect(barangayEl, 'Select Barangay');

        if (!region || !province || !addressData[region]?.[province]) {
            resetSelect(cityEl, 'Select City / Municipality');
            return;
        }

        populateSelect(cityEl, Object.keys(addressData[region][province]), 'Select City / Municipality', selectedCity);
    }

    function loadBarangays(region, province, city, selectedBarangay = '') {
        if (!region || !province || !city || !addressData[region]?.[province]?.[city]) {
            resetSelect(barangayEl, 'Select Barangay');
            return;
        }

        populateSelect(barangayEl, addressData[region][province][city], 'Select Barangay', selectedBarangay);
    }

    dentistCards.forEach(card => {
        card.addEventListener('click', function () {
            dentistCards.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            dentistInput.value = this.dataset.dentistValue || '';
            loadAvailableSlots();
        });
    });

    regionEl.addEventListener('change', function () {
        loadProvinces(this.value);
    });

    provinceEl.addEventListener('change', function () {
        loadCities(regionEl.value, this.value);
    });

    cityEl.addEventListener('change', function () {
        loadBarangays(regionEl.value, provinceEl.value, this.value);
    });

    serviceSelect.addEventListener('change', function () {
        updateServiceMetaFromOption();
        loadServiceQuestions();
        loadAvailableSlots();
    });

    prevMonthBtn.addEventListener('click', function () {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function () {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        renderCalendar();
    });

    updateServiceMetaFromOption();
    loadServiceQuestions();
    renderCalendar();

    loadRegions();

    if (oldRegion) {
        loadProvinces(oldRegion, oldProvince);
    } else {
        resetSelect(provinceEl, 'Select Province');
    }

    if (oldRegion && oldProvince) {
        loadCities(oldRegion, oldProvince, oldCity);
    } else {
        resetSelect(cityEl, 'Select City / Municipality');
    }

    if (oldRegion && oldProvince && oldCity) {
        loadBarangays(oldRegion, oldProvince, oldCity, oldBarangay);
    } else {
        resetSelect(barangayEl, 'Select Barangay');
    }

    if (dateInput.value) {
        selectedDateLabel.textContent = dateInput.value;
        loadAvailableSlots();
    }
});
</script>
@endsection
