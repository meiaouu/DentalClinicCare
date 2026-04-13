@extends('layouts.app')

@section('content')
<div style="
    min-height: calc(100vh - 110px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    background:
        radial-gradient(circle at top right, rgba(15,157,138,0.08), transparent 30%),
        linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
">
    <div style="
        width: 100%;
        max-width: 980px;
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        background: #ffffff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.10);
        border: 1px solid #e5e7eb;
    ">
        <div style="
            background: linear-gradient(135deg, #0b0f13 0%, #111827 55%, #0f9d8a 140%);
            color: #ffffff;
            padding: 48px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        ">
            <div style="
                position: absolute;
                top: -40px;
                right: -40px;
                width: 180px;
                height: 180px;
                border-radius: 50%;
                background: rgba(255,255,255,0.05);
            "></div>

            <div style="
                position: absolute;
                bottom: -50px;
                left: -50px;
                width: 220px;
                height: 220px;
                border-radius: 50%;
                background: rgba(255,255,255,0.04);
            "></div>

            <div style="
                position: relative;
                z-index: 1;
            ">
                <div style="
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    padding: 8px 14px;
                    border-radius: 999px;
                    background: rgba(255,255,255,0.08);
                    border: 1px solid rgba(255,255,255,0.12);
                    margin-bottom: 24px;
                    font-size: 13px;
                    font-weight: 700;
                    letter-spacing: 0.2px;
                ">
                    <span style="
                        display: inline-flex;
                        width: 10px;
                        height: 10px;
                        border-radius: 50%;
                        background: #34d399;
                    "></span>
                    Dental Clinic Guest Booking
                </div>

                <h1 style="
                    margin: 0 0 16px;
                    font-size: clamp(30px, 4vw, 44px);
                    line-height: 1.1;
                    font-weight: 800;
                    max-width: 420px;
                ">
                    Book your appointment in a few easy steps
                </h1>

                <p style="
                    margin: 0 0 28px;
                    color: rgba(255,255,255,0.82);
                    font-size: 15px;
                    line-height: 1.8;
                    max-width: 460px;
                ">
                    Continue as a guest using your mobile number. This helps the clinic review your appointment request quickly and contact you when needed.
                </p>

                <div style="
                    display: grid;
                    gap: 14px;
                    max-width: 420px;
                ">
                    <div style="
                        display: flex;
                        align-items: flex-start;
                        gap: 12px;
                        padding: 14px 16px;
                        border-radius: 16px;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(255,255,255,0.08);
                    ">
                        <div style="
                            width: 34px;
                            height: 34px;
                            border-radius: 10px;
                            background: rgba(15,157,138,0.18);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 14px;
                            flex-shrink: 0;
                        ">1</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Enter your mobile number</div>
                            <div style="font-size: 13px; color: rgba(255,255,255,0.75); line-height: 1.6;">
                                Use a valid Philippine number so the clinic can identify your booking.
                            </div>
                        </div>
                    </div>

                    <div style="
                        display: flex;
                        align-items: flex-start;
                        gap: 12px;
                        padding: 14px 16px;
                        border-radius: 16px;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(255,255,255,0.08);
                    ">
                        <div style="
                            width: 34px;
                            height: 34px;
                            border-radius: 10px;
                            background: rgba(15,157,138,0.18);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 14px;
                            flex-shrink: 0;
                        ">2</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Fill in appointment details</div>
                            <div style="font-size: 13px; color: rgba(255,255,255,0.75); line-height: 1.6;">
                                Choose your service, dentist, preferred date, and preferred time.
                            </div>
                        </div>
                    </div>

                    <div style="
                        display: flex;
                        align-items: flex-start;
                        gap: 12px;
                        padding: 14px 16px;
                        border-radius: 16px;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(255,255,255,0.08);
                    ">
                        <div style="
                            width: 34px;
                            height: 34px;
                            border-radius: 10px;
                            background: rgba(15,157,138,0.18);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 14px;
                            flex-shrink: 0;
                        ">3</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Wait for clinic review</div>
                            <div style="font-size: 13px; color: rgba(255,255,255,0.75); line-height: 1.6;">
                                The staff will review your request and confirm the schedule if available.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="
            padding: 34px 30px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        ">
            <div style="
    text-align: center;
    margin-bottom: 28px;
">
    <h2 style="
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #0f9d8a;
        letter-spacing: 0.3px;
    ">
        Guest Booking
    </h2>
</div>

            <div style="text-align: center; margin-bottom: 24px;">
                <div style="
                    width: 72px;
                    height: 72px;
                    margin: 0 auto 16px;
                    border-radius: 20px;
                    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 30px;
                    color: #0f9d8a;
                    box-shadow: inset 0 0 0 1px rgba(15,157,138,0.12);
                ">
                    📱
                </div>

                <h2 style="
                    margin: 0 0 8px;
                    font-size: 30px;
                    font-weight: 800;
                    color: #111827;
                    line-height: 1.1;
                ">
                    Guest Booking
                </h2>

                <p style="
                    margin: 0 auto;
                    max-width: 330px;
                    font-size: 14px;
                    color: #6b7280;
                    line-height: 1.7;
                ">
                    Enter your mobile number to continue. You can book without creating an account.
                </p>
            </div>

            @if ($errors->any())
                <div style="
                    margin-bottom: 18px;
                    border: 1px solid #fecaca;
                    background: #fef2f2;
                    color: #991b1b;
                    border-radius: 14px;
                    padding: 14px 16px;
                    font-size: 14px;
                ">
                    <div style="font-weight: 700; margin-bottom: 8px;">Please fix the following:</div>
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
                    text-align: left;
                    font-size: 13px;
                    font-weight: 800;
                    margin-bottom: 10px;
                    color: #1f2937;
                ">
                    Mobile Number
                </label>

                <div style="position: relative; margin-bottom: 10px;">
                    <span style="
                        position: absolute;
                        left: 16px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #0f9d8a;
                        font-size: 16px;
                    ">
                        +63
                    </span>

                    <input
                        type="text"
                        name="contact_number"
                        value="{{ old('contact_number') }}"
                        placeholder="09XXXXXXXXX"
                        required
                        style="
                            width: 100%;
                            height: 56px;
                            border-radius: 16px;
                            border: 1px solid #d1d5db;
                            padding: 0 16px 0 54px;
                            font-size: 15px;
                            color: #111827;
                            outline: none;
                            transition: 0.2s ease;
                            background: #ffffff;
                        "
                        onfocus="this.style.borderColor='#0f9d8a'; this.style.boxShadow='0 0 0 4px rgba(15,157,138,0.12)'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"
                    >
                </div>

                <div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 22px;
                    flex-wrap: wrap;
                ">
                    <div style="
                        font-size: 12px;
                        color: #9ca3af;
                        line-height: 1.6;
                    ">
                        Accepted format: 09XXXXXXXXX
                    </div>

                    <div style="
                        font-size: 12px;
                        color: #0f766e;
                        font-weight: 700;
                        background: #ecfdf5;
                        border: 1px solid #d1fae5;
                        padding: 6px 10px;
                        border-radius: 999px;
                    ">
                        Guest access only
                    </div>
                </div>

                <button type="submit" style="
                    width: 100%;
                    height: 56px;
                    border-radius: 16px;
                    border: none;
                    background: #0f9d8a;
                    color: #ffffff;
                    font-weight: 800;
                    font-size: 15px;
                    letter-spacing: 0.2px;
                    box-shadow: 0 12px 24px rgba(15,157,138,0.22);
                    cursor: pointer;
                    transition: 0.2s ease;
                "
                onmouseover="this.style.background='#0d8574'"
                onmouseout="this.style.background='#0f9d8a'">
                    Continue Booking
                </button>
            </form>

            <div style="
                margin-top: 20px;
                text-align: center;
                font-size: 14px;
                color: #6b7280;
                line-height: 1.8;
            ">
                Already have an account?
                <a href="{{ route('login') }}" style="color:#0f9d8a; font-weight:700;">Login</a>
                or
                <a href="{{ route('register') }}" style="color:#0f9d8a; font-weight:700;">Register</a>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) {
        .guest-booking-wrapper {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.querySelector('[data-guest-booking-wrapper]');
        if (wrapper && window.innerWidth <= 900) {
            wrapper.style.gridTemplateColumns = '1fr';
        }
    });
</script>
@endsection
