@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Booking Review</h2>

    <div class="card shadow-sm p-4 mb-4">
        <h5>Patient</h5>
        <p>{{ $booking['first_name'] }} {{ $booking['middle_name'] ?? '' }} {{ $booking['last_name'] }}</p>
        <p>{{ $booking['contact_number'] }} | {{ $booking['email'] }}</p>

        <h5 class="mt-4">Appointment</h5>
        <p>Date: {{ $booking['preferred_date'] }}</p>
        <p>Start Time: {{ $booking['preferred_start_time'] }}</p>
        <p>End Time: {{ $booking['preferred_end_time'] }}</p>
        <p>Service ID: {{ $booking['service_id'] }}</p>
        <p>Preferred Dentist ID: {{ $booking['preferred_dentist_id'] ?? 'Any available dentist' }}</p>

        @if(!empty($booking['notes']))
            <h5 class="mt-4">Notes</h5>
            <p>{{ $booking['notes'] }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('booking.confirm') }}">
        @csrf
        <input type="hidden" name="review_token" value="{{ $reviewToken }}">
        <button type="submit" class="btn btn-primary">Confirm Booking</button>
        <a href="{{ route('booking.create') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
