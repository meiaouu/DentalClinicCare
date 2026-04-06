@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
        <h4 class="mb-3 text-center">Book Appointment</h4>

        <form method="GET" action="{{ route('booking.guest.form') }}">
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text"
                       name="contact_number"
                       class="form-control"
                       placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX"
                       required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                Book Now
            </button>
        </form>

        <div class="text-center">
            <small>
                Already have an account?
                <a href="{{ route('login') }}">Login</a> or
                <a href="{{ route('register') }}">Register</a>
            </small>
        </div>
    </div>
</div>
@endsection
