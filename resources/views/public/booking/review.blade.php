@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        background: linear-gradient(180deg, #f7fbfb 0%, #f3f7f8 100%);
        min-height: 100vh;
        padding: 56px 16px 80px;
    }

    .booking-shell {
        max-width: 980px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .booking-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #e8f7f4;
        color: #0f766e;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .02em;
        margin-bottom: 14px;
    }

    .booking-title {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .booking-subtitle {
        max-width: 680px;
        margin: 0 auto;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
    }

    .booking-stepper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin: 28px 0 36px;
        flex-wrap: wrap;
    }

    .booking-step {
        display: flex;
        align-items: center;
        gap: 12px;
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
        border: 2px solid #dbe4ea;
        background: #fff;
        color: #94a3b8;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
    }

    .booking-step-circle.done,
    .booking-step-circle.active {
        border-color: #0f9d8a;
        background: #0f9d8a;
        color: #fff;
    }

    .booking-step-label {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
    }

    .booking-step-line {
        width: 72px;
        height: 2px;
        background: #dbe4ea;
        border-radius: 999px;
    }

    .booking-step-line.done {
        background: #0f9d8a;
    }

    .booking-card {
        background: #ffffff;
        border: 1px solid #e5eef1;
        border-radius: 28px;
        box-shadow: 0 18px 60px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .booking-card-body {
        padding: 28px;
    }

    .booking-section + .booking-section {
        margin-top: 22px;
    }

    .booking-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf2f5;
    }

    .booking-section-title h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .booking-section-badge {
        padding: 6px 10px;
        border-radius: 999px;
        background: #f0fdfa;
        color: #0f766e;
        font-size: 12px;
        font-weight: 700;
    }

    .booking-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 18px;
    }

    .booking-info-item {
        background: #f8fbfc;
        border: 1px solid #ecf2f4;
        border-radius: 18px;
        padding: 14px 16px;
        min-height: 78px;
    }

    .booking-info-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        margin-bottom: 8px;
    }

    .booking-info-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.6;
        word-break: break-word;
    }

    .booking-summary {
        background: linear-gradient(180deg, #fcfefe 0%, #f7fbfb 100%);
        border: 1px solid #e4efef;
        border-radius: 22px;
        padding: 18px 20px;
    }

    .booking-summary-row {
        display: grid;
        grid-template-columns: 190px 1fr;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #e9f0f2;
    }

    .booking-summary-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .booking-summary-key {
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .booking-summary-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
    }

    .booking-answer-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }

    .booking-answer-item {
        background: #f8fbfc;
        border: 1px solid #ecf2f4;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .booking-answer-item strong {
        color: #0f172a;
        display: block;
        margin-bottom: 6px;
    }

    .booking-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .booking-btn {
        min-width: 180px;
        height: 52px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 22px;
        border: none;
        text-decoration: none;
        transition: .2s ease;
    }

    .booking-btn-secondary {
        background: #f8fafc;
        border: 1px solid #d9e3e8;
        color: #334155;
    }

    .booking-btn-secondary:hover {
        background: #eef4f7;
        color: #0f172a;
    }

    .booking-btn-primary {
        background: linear-gradient(135deg, #0f9d8a 0%, #0b7c76 100%);
        color: #fff;
        box-shadow: 0 12px 24px rgba(15, 157, 138, 0.24);
    }

    .booking-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    @media (max-width: 767.98px) {
        .booking-page {
            padding-top: 36px;
        }

        .booking-card-body {
            padding: 20px;
        }

        .booking-info-grid {
            grid-template-columns: 1fr;
        }

        .booking-summary-row {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .booking-step-line {
            width: 40px;
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
