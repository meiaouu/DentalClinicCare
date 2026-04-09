@extends('layouts.app')

@section('content')
<style>
    .booking-page {
        background: linear-gradient(180deg, #f7fbfb 0%, #f3f7f8 100%);
        min-height: 100vh;
        padding: 56px 16px 80px;
    }

    .booking-shell {
        max-width: 860px;
        margin: 0 auto;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .booking-title {
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .booking-subtitle {
        max-width: 660px;
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
        border: 2px solid #0f9d8a;
        background: #0f9d8a;
        color: #fff;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
    }

    .booking-step-label {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
    }

    .booking-step-line {
        width: 72px;
        height: 2px;
        background: #0f9d8a;
        border-radius: 999px;
    }

    .booking-card {
        background: #ffffff;
        border: 1px solid #e5eef1;
        border-radius: 30px;
        box-shadow: 0 18px 60px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .booking-card-body {
        padding: 36px 28px;
        text-align: center;
    }

    .success-icon-wrap {
        width: 110px;
        height: 110px;
        margin: 0 auto 22px;
        border-radius: 999px;
        background: radial-gradient(circle at center, #dcfce7 0%, #d1fae5 65%, #ecfdf5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 0 10px rgba(255,255,255,0.65);
    }

    .success-icon {
        width: 52px;
        height: 52px;
        color: #16a34a;
    }

    .success-title {
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .success-message {
        max-width: 620px;
        margin: 0 auto 28px;
        color: #64748b;
        font-size: 16px;
        line-height: 1.8;
    }

    .success-panel {
        max-width: 620px;
        margin: 0 auto 28px;
        background: linear-gradient(180deg, #fcfefe 0%, #f7fbfb 100%);
        border: 1px solid #e4efef;
        border-radius: 22px;
        padding: 18px 20px;
        text-align: left;
    }

    .success-panel-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e9f0f2;
    }

    .success-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #e9f0f2;
    }

    .success-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .success-key {
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .success-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
    }

    .booking-actions {
        display: flex;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .booking-btn {
        min-width: 220px;
        height: 54px;
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

    .booking-btn-primary {
        background: linear-gradient(135deg, #0f9d8a 0%, #0b7c76 100%);
        color: #fff;
        box-shadow: 0 12px 24px rgba(15, 157, 138, 0.24);
    }

    .booking-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .booking-btn-link {
        min-width: unset;
        height: auto;
        padding: 0;
        background: transparent;
        color: #0f766e;
        font-weight: 700;
        text-decoration: none;
    }

    .booking-btn-link:hover {
        color: #0b5f5b;
        text-decoration: underline;
    }

    @media (max-width: 767.98px) {
        .booking-card-body {
            padding: 26px 20px;
        }

        .success-row {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .booking-step-line {
            width: 40px;
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
