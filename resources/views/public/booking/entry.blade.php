@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Book Appointment</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm p-4">
        <p class="mb-3">Choose how you want to continue:</p>

        <div class="d-grid gap-2">
            <a href="{{ route('booking.guest.form') }}" class="btn btn-primary">
                Book Now as Guest
            </a>

            <a href="{{ route('login', ['redirect' => route('booking.create')]) }}" class="btn btn-outline-secondary">
                Login
            </a>

            <a href="{{ route('register', ['redirect' => route('booking.create')]) }}" class="btn btn-outline-success">
                Register
            </a>
        </div>
    </div>
</div>
@endsection
