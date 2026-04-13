@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top right, rgba(15, 157, 138, 0.08), transparent 26%),
            linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
        padding: 42px 16px 72px;
    }

    .booking-container {
        max-width: 1120px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .booking-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #0f766e;
        font-size: 13px;
        font-weight: 800;
        border: 1px solid #d1fae5;
        margin-bottom: 14px;
    }

    .booking-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #10b981;
        display: inline-block;
    }

    .booking-title {
        margin: 0 0 10px;
        font-size: clamp(30px, 4vw, 42px);
        font-weight: 900;
        color: #0b0f13;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .booking-subtitle {
        margin: 0 auto;
        max-width: 760px;
        color: #6b7280;
        font-size: 15px;
        line-height: 1.8;
    }

    .booking-stepper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 26px;
        flex-wrap: wrap;
    }

    .booking-step {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .booking-step-circle {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        border: 2px solid #d1d5db;
        background: #ffffff;
        color: #6b7280;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }

    .booking-step-circle.active {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 157, 138, 0.20);
    }

    .booking-step-line {
        width: 68px;
        height: 2px;
        background: #dbe4ea;
        border-radius: 999px;
    }

    .booking-step-label {
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
    }

    .booking-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 28px;
        padding: 28px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.07);
    }

    .booking-section {
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 22px;
        background: #ffffff;
    }

    .booking-section + .booking-section {
        margin-top: 22px;
    }

    .booking-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid #eef2f7;
    }

    .booking-section-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .booking-section-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ecfdf5;
        color: #0f9d8a;
        font-weight: 900;
        font-size: 16px;
        border: 1px solid #d1fae5;
        flex-shrink: 0;
    }

    .booking-section-title {
        margin: 0;
        font-size: 19px;
        font-weight: 900;
        color: #111827;
        line-height: 1.2;
    }

    .booking-section-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    .booking-section-note {
        font-size: 12px;
        font-weight: 800;
        color: #0f766e;
        background: #ecfdf5;
        border-radius: 999px;
        padding: 7px 12px;
        white-space: nowrap;
        border: 1px solid #d1fae5;
    }

    .form-label {
        font-size: 13px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        height: 52px;
        border-radius: 14px;
        border: 1px solid #d1d5db;
        font-size: 15px;
        color: #111827;
        box-shadow: none !important;
        background: #ffffff;
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    textarea.form-control {
        min-height: 120px;
        height: auto;
        padding-top: 14px;
        resize: vertical;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0f9d8a;
        box-shadow: 0 0 0 4px rgba(15, 157, 138, 0.10) !important;
    }

    .booking-panel,
    .booking-calendar-panel,
    .booking-slot-panel,
    .booking-info-panel {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
    }

    .booking-panel-title {
        margin: 0 0 8px;
        font-size: 15px;
        font-weight: 900;
        color: #111827;
    }

    .booking-panel-text {
        font-size: 14px;
        line-height: 1.7;
        color: #4b5563;
    }

    .booking-info-panel {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .booking-info-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #ecfdf5;
        color: #0f9d8a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        flex-shrink: 0;
        border: 1px solid #d1fae5;
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
        font-weight: 900;
        color: #111827;
    }

    .calendar-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        font-weight: 900;
        transition: 0.2s ease;
    }

    .calendar-btn:hover {
        border-color: #0f9d8a;
        color: #0f9d8a;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 10px;
        text-align: center;
        font-size: 12px;
        font-weight: 900;
        color: #6b7280;
    }

    #bookingCalendarGrid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    #bookingCalendarGrid button {
        min-height: 46px;
        border-radius: 12px;
        font-weight: 800;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        transition: 0.2s ease;
    }

    #bookingCalendarGrid button:hover:not(:disabled) {
        border-color: #0f9d8a;
        color: #0f9d8a;
    }

    #bookingCalendarGrid .btn-primary {
        background: #0f9d8a !important;
        border-color: #0f9d8a !important;
        color: #ffffff !important;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .time-slot-btn {
        border: 1px solid #dbe4ea;
        background: #ffffff;
        border-radius: 14px;
        min-height: 48px;
        padding: 10px;
        font-weight: 800;
        text-align: center;
        color: #374151;
        cursor: pointer;
        transition: 0.2s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .time-slot-btn:hover:not(.disabled) {
        border-color: #0f9d8a;
        color: #0f9d8a;
        background: #f0fdfa;
    }

    .time-slot-btn.active {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
        box-shadow: 0 10px 18px rgba(15,157,138,0.18);
    }

    .time-slot-btn.disabled {
        background: #f9fafb;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
        box-shadow: none;
    }

    .booking-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 28px;
    }

    .booking-primary-btn {
        min-width: 230px;
        height: 54px;
        border: none;
        border-radius: 16px;
        background: #0f9d8a;
        color: #ffffff;
        font-size: 15px;
        font-weight: 900;
        transition: 0.2s ease;
        box-shadow: 0 12px 24px rgba(15,157,138,0.20);
    }

    .booking-primary-btn:hover {
        background: #0d8574;
    }

    .alert {
        border-radius: 18px;
        border: 1px solid #fecaca;
        background: #fff5f5;
    }

    .form-check {
        padding: 12px 12px 12px 36px;
        border: 1px solid #e5edf2;
        border-radius: 14px;
        background: #ffffff;
        margin-bottom: 8px;
    }

    .small.text-muted {
        color: #6b7280 !important;
    }

    @media (max-width: 991.98px) {
        .slot-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .booking-page {
            padding: 26px 12px 56px;
        }

        .booking-card {
            padding: 18px;
            border-radius: 22px;
        }

        .booking-section {
            padding: 16px;
            border-radius: 18px;
        }

        .booking-step-line {
            width: 34px;
        }

        .slot-grid {
            grid-template-columns: 1fr;
        }

        .booking-actions {
            justify-content: stretch;
        }

        .booking-primary-btn {
            width: 100%;
            min-width: 100%;
        }

        .booking-section-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="booking-page">
    <div class="booking-container">
        <div class="booking-header">
            <div class="booking-badge">
                <span class="booking-badge-dot"></span>
                {{ $isGuest ? 'Guest Appointment Request' : 'Patient Appointment Request' }}
            </div>

            <h1 class="booking-title">{{ $isGuest ? 'Guest Booking Form' : 'Book Appointment' }}</h1>

            <p class="booking-subtitle">
                Fill in your information, choose a dental service, and pick your preferred date and time.
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
                <div class="alert alert-danger mb-4 p-3">
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
                        <div class="booking-section-title-wrap">
                            <div class="booking-section-icon">1</div>
                            <div>
                                <h5 class="booking-section-title">Patient Information</h5>
                                <p class="booking-section-subtitle">Basic personal and contact details for your appointment request.</p>
                            </div>
                        </div>
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
                        <div class="booking-section-title-wrap">
                            <div class="booking-section-icon">2</div>
                            <div>
                                <h5 class="booking-section-title">Address</h5>
                                <p class="booking-section-subtitle">Your current address information for clinic records.</p>
                            </div>
                        </div>
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
                        <div class="booking-section-title-wrap">
                            <div class="booking-section-icon">3</div>
                            <div>
                                <h5 class="booking-section-title">Appointment Details</h5>
                                <p class="booking-section-subtitle">Choose a service, date, time, and additional request details.</p>
                            </div>
                        </div>
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

                        <input type="hidden" name="preferred_dentist_id" id="preferred_dentist_id" value="">

                        <div class="col-12 mb-3">
                            <div class="booking-info-panel">
                                <div class="booking-info-icon">i</div>
                                <div>
                                    <h6 class="booking-panel-title mb-1">Doctor Assignment</h6>
                                    <div class="booking-panel-text">
                                        Dentist selection is handled by the clinic during review and confirmation to match the best available schedule.
                                    </div>
                                </div>
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

                            <div class="booking-calendar-panel">
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
        const date = dateInput.value;

        hiddenTimeInput.value = '';
        timeSlotGrid.innerHTML = '';

        if (!serviceId || !date) {
            slotFeedback.textContent = 'Select a service and date to load available times.';
            return;
        }

        const url = `{{ route('booking.available.slots') }}?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;

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
