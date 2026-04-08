@extends('layouts.app')

@section('content')
<style>
    .booking-shell {
        min-height: 100vh;
        padding: 40px 20px 80px;
        background:
            linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(30, 41, 59, 0.82) 100%),
            url('{{ asset("images/dentalimg.jpg") }}') center center / cover no-repeat;
    }

    .booking-wrap {
        max-width: 1180px;
        margin: 0 auto;
    }

    .booking-header {
        color: #ffff;
        margin-bottom: 28px;
    }

    .booking-eyebrow {
        font-size: 16px;
        color: #cbd5e1;
        margin-bottom: 10px;
    }

    .booking-main-title {
        font-size: clamp(34px, 5vw, 52px);
        font-weight: 800;
        line-height: 1.08;
        margin-bottom: 12px;
    }

    .booking-main-text {
        max-width: 760px;
        font-size: 17px;
        line-height: 1.7;
        color: #e2e8f0;
        margin-bottom: 0;
    }

    .form-theme-card {
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        backdrop-filter: blur(8px);
    }

    .section-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 22px;
        margin-bottom: 22px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        height: 52px;
        border-radius: 14px;
        border: 1px solid #d1d5db;
        font-size: 15px;
        box-shadow: none !important;
    }

    textarea.form-control {
        min-height: 120px;
        height: auto;
        padding-top: 12px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10) !important;
    }

    .meta-box,
    .light-panel {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        background: #f8fafc;
    }

    .meta-box h6,
    .light-panel h6 {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .calendar-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .calendar-toolbar h6 {
        margin: 0;
        font-weight: 800;
        color: #0f172a;
    }

    .calendar-btn {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #334155;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-weight: 700;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-align: center;
    }

    #bookingCalendarGrid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }

    #bookingCalendarGrid button {
        min-height: 42px;
        border-radius: 10px;
        font-weight: 700;
    }

    .time-slot-btn {
        border: 1px solid #dbe3ea;
        background: #ffffff;
        border-radius: 12px;
        padding: 12px 10px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #334155;
    }

    .time-slot-btn:hover:not(.disabled) {
        border-color: #2563eb;
        color: #2563eb;
    }

    .time-slot-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .time-slot-btn.disabled {
        background: #f1f5f9;
        color: #9aa4af;
        border-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.75;
    }

    .submit-wrap {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .review-btn {
        min-width: 220px;
        height: 54px;
        border: none;
        border-radius: 999px;
        background: #2563eb;
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
        transition: 0.2s ease;
    }

    .review-btn:hover {
        background: #1d4ed8;
    }

    .alert {
        border-radius: 16px;
    }

    @media (max-width: 768px) {
        .booking-shell {
            padding: 24px 14px 60px;
        }

        .form-theme-card,
        .section-card {
            padding: 18px;
        }

        .submit-wrap {
            justify-content: stretch;
        }

        .review-btn {
            width: 100%;
        }
    }
</style>

<div class="booking-shell">
    <div class="booking-wrap">
        <div class="booking-header">
            <div class="booking-eyebrow">Appointment Booking</div>
            <h1 class="booking-main-title">
                {{ $isGuest ? 'Guest Booking Form' : 'Book Appointment' }}
            </h1>
            <p class="booking-main-text">
                Fill in your information, choose your dental service, select an available date and time,
                and review your appointment request before submission.
            </p>
        </div>

        <div class="form-theme-card">
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

                <div class="section-card">
                    <h5 class="section-title">Patient Information</h5>

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

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_number" class="form-control"
                                placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX"
                                value="{{ old('emergency_contact_number', $patient->emergency_contact_number ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="section-card">
    <h5 class="section-title">Address</h5>

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
            <input
                type="text"
                name="address_line"
                class="form-control"
                value="{{ old('address_line') }}"
                placeholder="Street / House No. / Unit"
            >
        </div>
    </div>
</div>

                <div class="section-card">
                    <h5 class="section-title">Appointment Details</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
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

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preferred Dentist</label>
                            <select name="preferred_dentist_id" id="preferred_dentist_id" class="form-select">
                                <option value="">Any Available Dentist</option>
                                @forelse(($dentists ?? []) as $dentist)
                                    <option value="{{ $dentist->dentist_id }}"
                                        {{ (string) old('preferred_dentist_id') === (string) $dentist->dentist_id ? 'selected' : '' }}>
                                        Dentist #{{ $dentist->dentist_id }}
                                    </option>
                                @empty
                                    <option value="" disabled>No available dentists found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="meta-box">
                                <h6>Service Information</h6>
                                <div id="serviceMetaDescription">Select a service to view the description.</div>
                                <div class="mt-2">
                                    <strong>Estimated Duration:</strong> <span id="serviceMetaDuration">-</span><br>
                                    <strong>Estimated Price:</strong> ₱<span id="serviceMetaPrice">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Service-Specific Questions</label>
                            <div id="dynamicQuestions" class="light-panel">
                                <p class="text-muted mb-0">Select a service to load additional questions.</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <input type="hidden" name="preferred_date" id="preferred_date"
                                   value="{{ old('preferred_date') }}"
                                   min="{{ now()->toDateString() }}">

                            <div class="light-panel h-100">
                                <div class="calendar-toolbar">
                                    <button type="button" class="calendar-btn" id="prevMonthBtn">&lt;</button>
                                    <h6 id="calendarMonthLabel">Select Date</h6>
                                    <button type="button" class="calendar-btn" id="nextMonthBtn">&gt;</button>
                                </div>

                                <div class="calendar-weekdays">
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                </div>

                                <div id="bookingCalendarGrid"></div>

                                <div class="mt-3">
                                    <strong>Selected Date:</strong>
                                    <span id="selectedDateLabel">{{ old('preferred_date') ?: 'None' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="light-panel h-100">
                                <label class="form-label fw-bold">Available Time Slots</label>
                                <input type="hidden" name="preferred_start_time" id="preferred_start_time" value="{{ old('preferred_start_time') }}">
                                <div id="timeSlotGrid" class="d-grid" style="grid-template-columns: repeat(4, 1fr); gap: 10px;"></div>
                                <div id="slotFeedback" class="small text-muted mt-3">
                                    Select a service and date to load available times.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-0">
                            <label class="form-label">Notes / Concerns</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="submit-wrap">
                    <button type="submit" class="review-btn">Review Booking</button>
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
    const dentistSelect = document.getElementById('preferred_dentist_id');
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
        const dentistId = dentistSelect.value;
        const date = dateInput.value;

        hiddenTimeInput.value = '';
        timeSlotGrid.innerHTML = '';

        if (!serviceId || !date) {
            slotFeedback.textContent = 'Select a service and date to load available times.';
            return;
        }

        let url = `{{ route('booking.slots') }}?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;
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

    dentistSelect.addEventListener('change', loadAvailableSlots);

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
