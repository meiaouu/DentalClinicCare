@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="alert alert-success">
        <h3>Appointment Request Submitted Successfully</h3>
        <p>Your booking request has been saved as <strong>Pending</strong>.</p>
        <p><strong>Request Code:</strong> {{ $booking->request_code }}</p>
        <p><strong>Status:</strong> {{ ucfirst($booking->request_status) }}</p>
        <p>Please wait for staff review and confirmation.</p>
    </div>

    <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
</div>
@endsection
