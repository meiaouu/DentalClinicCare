@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Booking Review</h2>

    <div class="card shadow-sm p-4">
   

        <h5>Patient Information</h5>
        <p><strong>Name:</strong> {{ $data['first_name'] }} {{ $data['middle_name'] ?? '' }} {{ $data['last_name'] }}</p>
        <p><strong>Sex:</strong> {{ ucfirst($data['sex']) }}</p>
        <p><strong>Birth Date:</strong> {{ $data['birth_date'] }}</p>
        <p><strong>Civil Status:</strong> {{ ucfirst($data['civil_status']) }}</p>
        <p><strong>Occupation:</strong> {{ $data['occupation'] ?? 'N/A' }}</p>
        <p><strong>Contact Number:</strong> {{ $data['contact_number'] ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $data['email'] ?? 'N/A' }}</p>
        <p><strong>Emergency Contact Name:</strong> {{ $data['emergency_contact_name'] ?? 'N/A' }}</p>
        <p><strong>Emergency Contact Number:</strong> {{ $data['emergency_contact_number'] ?? 'N/A' }}</p>

        <hr>

        <h5>Address</h5>
        <p>
            {{ $data['address_line'] ?? '' }}
            {{ $data['barangay'] }},
            {{ $data['city'] }},
            {{ $data['province'] }},
            {{ $data['region'] }}
        </p>

        <hr>

        <h5>Appointment Details</h5>
        <p><strong>Service:</strong> {{ $service->service_name }}</p>
        <p><strong>Description:</strong> {{ $service->description }}</p>
        <p><strong>Estimated Duration:</strong> {{ $service->estimated_duration_minutes }} minutes</p>
        <p><strong>Estimated Price:</strong> ₱{{ number_format((float) $service->estimated_price, 2) }}</p>
        <p><strong>Preferred Dentist:</strong> {{ $dentist ? 'Dentist #' . $dentist->dentist_id : 'Any Available Dentist' }}</p>
        <p><strong>Date:</strong> {{ $data['preferred_date'] }}</p>
        <p><strong>Time:</strong> {{ $data['preferred_start_time'] }}</p>
        <p><strong>Notes:</strong> {{ $data['notes'] ?? 'N/A' }}</p>

        <hr>

        <h5>Service Answers</h5>
        @if(!empty($data['answers']) && is_array($data['answers']))
            <ul>
                @foreach($data['answers'] as $optionId => $answer)
                    <li>
                        <strong>Option #{{ $optionId }}:</strong>
                        @if(is_array($answer))
                            {{ implode(', ', $answer) }}
                        @else
                            {{ $answer }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>No extra answers submitted.</p>
        @endif

        <div class="d-flex gap-2 mt-4">
            <button type="button" onclick="history.back()" class="btn btn-outline-secondary">
                Edit
            </button>

            <form method="POST" action="{{ route('booking.store') }}">
                @csrf
                <button type="submit" class="btn btn-success">
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
