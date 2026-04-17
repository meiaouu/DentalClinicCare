@extends('layouts.app')

@section('content')
<div style="
    min-height: calc(100vh - 110px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 15px;
    background: #f5f5f5;
">
    <div style="
        width: 100%;
        max-width: 900px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: hidden;
    ">
        <div style="
            background: #0f766e;
            color: white;
            padding: 30px 25px;
        ">
            <h1 style="
                margin: 0 0 15px 0;
                font-size: 30px;
                font-weight: bold;
                line-height: 1.3;
            ">
                Guest Booking
            </h1>

            <p style="
                margin: 0 0 25px 0;
                font-size: 14px;
                line-height: 1.7;
                color: rgba(255,255,255,0.9);
            ">
                You can continue your dental clinic appointment booking as a guest by entering your mobile number.
            </p>

            <div style="
                background: rgba(255,255,255,0.12);
                border: 1px solid rgba(255,255,255,0.15);
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 12px;
            ">
                <strong>Step 1</strong>
                <div style="margin-top: 6px; font-size: 13px; line-height: 1.6;">
                    Enter your active mobile number.
                </div>
            </div>

            <div style="
                background: rgba(255,255,255,0.12);
                border: 1px solid rgba(255,255,255,0.15);
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 12px;
            ">
                <strong>Step 2</strong>
                <div style="margin-top: 6px; font-size: 13px; line-height: 1.6;">
                    Fill in your appointment and personal details.
                </div>
            </div>

            <div style="
                background: rgba(255,255,255,0.12);
                border: 1px solid rgba(255,255,255,0.15);
                padding: 15px;
                border-radius: 8px;
            ">
                <strong>Step 3</strong>
                <div style="margin-top: 6px; font-size: 13px; line-height: 1.6;">
                    Wait for the clinic staff to review your request.
                </div>
            </div>
        </div>

        <div style="
            padding: 30px 25px;
            background: #ffffff;
        ">
            <h2 style="
                margin: 0 0 8px 0;
                font-size: 24px;
                font-weight: bold;
                color: #111827;
            ">
                Continue as Guest
            </h2>

            <p style="
                margin: 0 0 20px 0;
                font-size: 14px;
                color: #6b7280;
                line-height: 1.7;
            ">
                Enter your mobile number to continue your booking without creating an account.
            </p>

            @if ($errors->any())
                <div style="
                    margin-bottom: 18px;
                    border: 1px solid #f5c2c7;
                    background: #fff1f2;
                    color: #991b1b;
                    border-radius: 8px;
                    padding: 12px 14px;
                    font-size: 14px;
                ">
                    <div style="font-weight: bold; margin-bottom: 8px;">Please check the following:</div>
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li style="margin-bottom: 4px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('booking.guest.form') }}">
                <label style="
                    display: block;
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 8px;
                    color: #1f2937;
                ">
                    Mobile Number
                </label>

                <input
                    type="text"
                    name="contact_number"
                    value="{{ old('contact_number') }}"
                    placeholder="09XXXXXXXXX"
                    required
                    style="
                        width: 100%;
                        height: 48px;
                        border-radius: 8px;
                        border: 1px solid #cbd5e1;
                        padding: 0 12px;
                        font-size: 14px;
                        color: #111827;
                        outline: none;
                        box-sizing: border-box;
                        margin-bottom: 10px;
                    "
                >

                <div style="
                    font-size: 12px;
                    color: #6b7280;
                    margin-bottom: 18px;
                ">
                    Example: 09123456789
                </div>

                <button type="submit" style="
                    width: 100%;
                    height: 48px;
                    border-radius: 8px;
                    border: none;
                    background: #0f766e;
                    color: #ffffff;
                    font-weight: bold;
                    font-size: 14px;
                    cursor: pointer;
                ">
                    Continue Booking
                </button>
            </form>

            <div style="
                margin-top: 18px;
                font-size: 14px;
                color: #6b7280;
                line-height: 1.7;
                text-align: center;
            ">
                Already have an account?
                <a href="{{ route('login') }}" style="color:#0f766e; font-weight:bold; text-decoration:none;">Login</a>
                or
                <a href="{{ route('register') }}" style="color:#0f766e; font-weight:bold; text-decoration:none;">Register</a>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) {
        .guest-booking-simple-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainCard = document.querySelector('[data-guest-booking-simple]');
        if (mainCard && window.innerWidth <= 900) {
            mainCard.style.gridTemplateColumns = '1fr';
        }
    });
</script>
@endsection
