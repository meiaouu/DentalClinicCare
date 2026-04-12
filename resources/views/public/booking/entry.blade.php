@extends('layouts.app')

@section('content')
<div style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: #f4f5f7;
">
    <div style="
        width: 100%;
        max-width: 430px;
        background: #ffffff;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.10);
        border: 1px solid #e5e7eb;
    ">

        <div style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        ">
            <div style="
                text-align: center;
                padding: 16px;
                font-weight: 600;
                color: #6b7280;
            ">
                Login / Register
            </div>

            <div style="
                text-align: center;
                padding: 16px;
                font-weight: 600;
                background: #ffffff;
                color: #111827;
                border-bottom: 3px solid #d1d5db;
            ">
                Book as Guest
            </div>
        </div>

        <div style="padding: 36px 28px 30px; text-align: center;">
            <div style="
                width: 62px;
                height: 62px;
                margin: 0 auto 18px;
                border-radius: 50%;
                background: #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
            ">
                📞
            </div>

            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">
                Welcome
            </h1>

            <p style="
                font-size: 14px;
                color: #6b7280;
                margin-bottom: 28px;
            ">
                Enter your mobile number to continue booking.
            </p>

            <form method="GET" action="{{ route('booking.guest.form') }}">
                <label style="
                    display: block;
                    text-align: left;
                    font-size: 13px;
                    font-weight: 700;
                    margin-bottom: 8px;
                ">
                    Mobile Number
                </label>

                <div style="position: relative; margin-bottom: 12px;">
                    <span style="
                        position: absolute;
                        top: 50%;
                        right: 14px;
                        transform: translateY(-50%);
                        color: #9ca3af;
                    ">
                        📱
                    </span>

                    <input
                        type="text"
                        name="contact_number"
                        placeholder="09XXXXXXXXX"
                        required
                        style="
                            text-align: right;
                            width: 100%;
                            height: 52px;
                            border-radius: 14px;
                            border: 1px solid #d1d5db;
                            padding-right: 44px;
                            font-size: 15px;
                        "
                    >
                </div>

                <div style="
                    text-align: right;
                    font-size: 12px;
                    color: #9ca3af;
                    margin-bottom: 18px;
                ">
                    PH format only
                </div>

                <button type="submit" style="
                    width: 100%;
                    height: 52px;
                    border-radius: 14px;
                    border: none;
                    background: #111827;
                    color: #fff;
                    font-weight: 700;
                ">
                    Book Now
                </button>
            </form>

            <div style="margin-top: 18px; font-size: 14px; color: #6b7280;">
                Already have an account?
                <a href="{{ route('login') }}">Login</a> or
                <a href="{{ route('register') }}">Register</a>
            </div>
        </div>
    </div>
</div>
@endsection
