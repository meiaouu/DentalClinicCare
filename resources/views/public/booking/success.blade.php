@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        background: #f3f4f6;
        min-height: 100vh;
        padding: 24px 12px 40px;
    }

    .booking-shell {
        max-width: 800px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 20px;
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
        border: 1px solid #0f766e;
        background: #0f766e;
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
        background: #0f766e;
    }

    .booking-card {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: hidden;
    }

    .booking-card-body {
        padding: 28px 20px;
        text-align: center;
    }

    .success-icon-wrap {
        width: 80px;
        height: 80px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success-icon {
        width: 40px;
        height: 40px;
        color: #16a34a;
    }

    .success-title {
        font-size: 26px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .success-message {
        max-width: 560px;
        margin: 0 auto 20px;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.7;
    }

    .success-panel {
        max-width: 560px;
        margin: 0 auto 22px;
        background: #fafafa;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 14px;
        text-align: left;
    }

    .success-panel-title {
        font-size: 16px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .success-row {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .success-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .success-key {
        color: #6b7280;
        font-size: 13px;
        font-weight: bold;
    }

    .success-value {
        color: #111827;
        font-size: 14px;
        font-weight: 600;
        word-break: break-word;
    }

    .booking-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-btn {
        min-width: 180px;
        height: 46px;
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

    .booking-btn-primary {
        background: #0f766e;
        color: #ffffff;
    }

    .booking-btn-primary:hover {
        color: #ffffff;
        background: #0b5f5b;
    }

    @media (max-width: 767.98px) {
        .booking-page {
            padding: 18px 10px 30px;
        }

        .booking-card-body {
            padding: 22px 14px;
        }

        .success-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .booking-actions {
            flex-direction: column;
        }

        .booking-btn {
            width: 100%;
        }
    }
</style>

<div class="booking-page">
    <div class="booking-shell">
        <div class="booking-header">
            <h1 class="booking-title">Appointment request submitted</h1>
            <p class="booking-subtitle">
                Your booking has been saved successfully and is now waiting for staff review.
            </p>

            <div class="booking-stepper" aria-label="Booking progress">
                <div class="booking-step">
                    <span class="booking-step-circle">1</span>
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
            <div class="booking-card-body">
                <div class="success-icon-wrap">
                    <svg class="success-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2 class="success-title">Booking submitted successfully</h2>

                <p class="success-message">
                    Your booking request has been saved as <strong>Pending</strong>.
                    Please wait for staff review and confirmation.
                </p>

                <div class="success-panel">
                    <div class="success-panel-title">Request Summary</div>

                    <div class="success-row">
                        <div class="success-key">Request Code</div>
                        <div class="success-value">{{ $booking->request_code }}</div>
                    </div>

                    <div class="success-row">
                        <div class="success-key">Current Status</div>
                        <div class="success-value">{{ ucfirst($booking->request_status) }}</div>
                    </div>
                </div>

                <div class="booking-actions">
                    <a href="{{ route('home') }}" class="booking-btn booking-btn-primary">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
