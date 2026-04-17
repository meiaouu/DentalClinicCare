@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        background: #f3f4f6;
        min-height: 100vh;
        padding: 24px 12px 40px;
    }

    .booking-shell {
        max-width: 950px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .booking-eyebrow {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        background: #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .booking-title {
        font-size: 28px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .booking-subtitle {
        max-width: 620px;
        margin: 0 auto;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .booking-stepper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin: 20px 0 28px;
        flex-wrap: wrap;
    }

    .booking-step {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .booking-step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: bold;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #6b7280;
    }

    .booking-step-circle.done,
    .booking-step-circle.active {
        background: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
    }

    .booking-step-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .booking-step-line {
        width: 20px;
        height: 1px;
        background: #cbd5e1;
    }

    .booking-step-line.done {
        background: #0f766e;
    }

    .booking-card {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: hidden;
    }

    .booking-card-body {
        padding: 18px;
    }

    .booking-section + .booking-section {
        margin-top: 16px;
    }

    .booking-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .booking-section-title h5 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
        color: #111827;
    }

    .booking-section-badge {
        padding: 6px 10px;
        border-radius: 20px;
        background: #f3f4f6;
        color: #374151;
        font-size: 11px;
        font-weight: bold;
        border: 1px solid #d1d5db;
    }

    .booking-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .booking-info-item {
        background: #fafafa;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .booking-info-label {
        font-size: 12px;
        font-weight: bold;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .booking-info-value {
        color: #111827;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.6;
        word-break: break-word;
    }

    .booking-summary {
        background: #fafafa;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 14px;
    }

    .booking-summary-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .booking-summary-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .booking-summary-key {
        color: #6b7280;
        font-size: 13px;
        font-weight: bold;
    }

    .booking-summary-value {
        color: #111827;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.6;
        word-break: break-word;
    }

    .booking-answer-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 10px;
    }

    .booking-answer-item {
        background: #fafafa;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .booking-answer-item strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .booking-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .booking-btn {
        min-width: 160px;
        height: 44px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border: none;
        text-decoration: none;
    }

    .booking-btn-secondary {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .booking-btn-primary {
        background: #0f766e;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .booking-page {
            padding: 18px 10px 30px;
        }

        .booking-card-body {
            padding: 14px;
        }

        .booking-info-grid {
            grid-template-columns: 1fr;
        }

        .booking-summary-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .booking-actions {
            flex-direction: column-reverse;
        }

        .booking-btn {
            width: 100%;
        }
    }
</style>

<div class="booking-page">
    <div class="booking-shell">
        <div class="booking-header">
            <div class="booking-eyebrow">Clinic Appointment Flow</div>
            <h1 class="booking-title">Review your booking details</h1>
            <p class="booking-subtitle">
                Please check all information before submitting your appointment request.
            </p>

            <div class="booking-stepper" aria-label="Booking progress">
                <div class="booking-step">
                    <span class="booking-step-circle done">1</span>
                    <span class="booking-step-label">Fill Up</span>
                </div>

                <span class="booking-step-line done"></span>

                <div class="booking-step">
                    <span class="booking-step-circle active">2</span>
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
            <div class="booking-card-body">

                <section class="booking-section">
                    <div class="booking-section-title">
                        <h5>Patient Information</h5>
                        <span class="booking-section-badge">{{ $isGuest ? 'Guest Booking' : 'Registered Patient' }}</span>
                    </div>

                    <div class="booking-info-grid">
                        <div class="booking-info-item">
                            <div class="booking-info-label">First Name</div>
                            <div class="booking-info-value">{{ $data['first_name'] ?? $data['guest_first_name'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Middle Name</div>
                            <div class="booking-info-value">{{ $data['middle_name'] ?? $data['guest_middle_name'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Last Name</div>
                            <div class="booking-info-value">{{ $data['last_name'] ?? $data['guest_last_name'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Sex</div>
                            <div class="booking-info-value">{{ !empty($data['sex']) ? ucfirst($data['sex']) : 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Birth Date</div>
                            <div class="booking-info-value">{{ $data['birth_date'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Civil Status</div>
                            <div class="booking-info-value">{{ !empty($data['civil_status']) ? ucfirst($data['civil_status']) : 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Occupation</div>
                            <div class="booking-info-value">{{ $data['occupation'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Contact Number</div>
                            <div class="booking-info-value">
                                {{ $data['contact_number'] ?? $data['guest_contact_number'] ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Email</div>
                            <div class="booking-info-value">{{ $data['email'] ?? $data['guest_email'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Emergency Contact Name</div>
                            <div class="booking-info-value">{{ $data['emergency_contact_name'] ?? 'N/A' }}</div>
                        </div>

                        <div class="booking-info-item">
                            <div class="booking-info-label">Emergency Contact Number</div>
                            <div class="booking-info-value">{{ $data['emergency_contact_number'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </section>

                <section class="booking-section">
                    <div class="booking-section-title">
                        <h5>Address</h5>
                        <span class="booking-section-badge">Location Details</span>
                    </div>

                    <div class="booking-summary">
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Complete Address</div>
                            <div class="booking-summary-value">
                                {{ $data['address_line'] ?? '' }}
                                {{ !empty($data['barangay']) ? $data['barangay'] . ',' : '' }}
                                {{ !empty($data['city']) ? ' ' . $data['city'] . ',' : '' }}
                                {{ !empty($data['province']) ? ' ' . $data['province'] . ',' : '' }}
                                {{ $data['region'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </section>

                <section class="booking-section">
                    <div class="booking-section-title">
                        <h5>Appointment Details</h5>
                        <span class="booking-section-badge">Service & Schedule</span>
                    </div>

                    <div class="booking-summary">
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Service</div>
                            <div class="booking-summary-value">{{ $service->service_name }}</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Description</div>
                            <div class="booking-summary-value">{{ $service->description ?: 'N/A' }}</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Estimated Duration</div>
                            <div class="booking-summary-value">{{ $service->estimated_duration_minutes }} minutes</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Estimated Price</div>
                            <div class="booking-summary-value">₱{{ number_format((float) $service->estimated_price, 2) }}</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Preferred Dentist</div>
                            <div class="booking-summary-value">
                                {{ $dentist ? 'Dentist #' . $dentist->dentist_id : 'Any Available Dentist' }}
                            </div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Preferred Date</div>
                            <div class="booking-summary-value">{{ $data['preferred_date'] }}</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Preferred Time</div>
                            <div class="booking-summary-value">{{ $data['preferred_start_time'] }}</div>
                        </div>
                        <div class="booking-summary-row">
                            <div class="booking-summary-key">Notes</div>
                            <div class="booking-summary-value">{{ $data['notes'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </section>

                <section class="booking-section">
                    <div class="booking-section-title">
                        <h5>Service Answers</h5>
                        <span class="booking-section-badge">Dynamic Questions</span>
                    </div>

                    @if(!empty($data['answers']) && is_array($data['answers']))
                        <ul class="booking-answer-list">
                            @foreach($data['answers'] as $optionId => $answer)
                                <li class="booking-answer-item">
                                    <strong>Option #{{ $optionId }}</strong>
                                    <span>
                                        @if(is_array($answer))
                                            {{ implode(', ', $answer) }}
                                        @else
                                            {{ $answer }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="booking-summary">
                            <div class="booking-summary-row">
                                <div class="booking-summary-key">Submitted Answers</div>
                                <div class="booking-summary-value">No extra answers submitted.</div>
                            </div>
                        </div>
                    @endif
                </section>

                <div class="booking-actions">
                    <button type="button" onclick="history.back()" class="booking-btn booking-btn-secondary">
                        Edit Details
                    </button>

                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf
                        <button type="submit" class="booking-btn booking-btn-primary">
                            Confirm Booking
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
