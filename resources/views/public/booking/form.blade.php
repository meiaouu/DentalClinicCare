@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">{{ $isGuest ? 'Guest Booking Form' : 'Book Appointment' }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('booking.review') }}" id="bookingForm">
        @csrf

        @if($isGuest)
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Guest Verification</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest First Name</label>
                        <input type="text" name="guest_first_name" class="form-control" value="{{ old('guest_first_name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Middle Name</label>
                        <input type="text" name="guest_middle_name" class="form-control" value="{{ old('guest_middle_name') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Last Name</label>
                        <input type="text" name="guest_last_name" class="form-control" value="{{ old('guest_last_name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Mobile Number</label>
                        <input type="text" name="guest_contact_number" class="form-control" placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX" value="{{ old('guest_contact_number') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Email</label>
                        <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email') }}">
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow-sm p-3 mb-4">
            <h5 class="mb-3">Patient Information</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control"
                        value="{{ old('first_name', $patient->first_name ?? '') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control"
                        value="{{ old('middle_name', $patient->middle_name ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control"
                        value="{{ old('last_name', $patient->last_name ?? '') }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-control" required>
                        <option value="">Select Sex</option>
                        <option value="male" {{ old('sex', $patient->sex ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex', $patient->sex ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Birth Date</label>
                    <input type="date" name="birth_date" class="form-control"
                        value="{{ old('birth_date', $patient->birth_date ?? '') }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Civil Status</label>
                    <select name="civil_status" class="form-control" required>
                        <option value="">Select Civil Status</option>
                        @php $civilStatus = old('civil_status', $patient->civil_status ?? ''); @endphp
                        <option value="single" {{ $civilStatus === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ $civilStatus === 'married' ? 'selected' : '' }}>Married</option>
                        <option value="widowed" {{ $civilStatus === 'widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="separated" {{ $civilStatus === 'separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Occupation</label>
                    <input type="text" name="occupation" class="form-control"
                        value="{{ old('occupation', $patient->occupation ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control"
                        placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX"
                        value="{{ old('contact_number', $patient->contact_number ?? '') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $patient->email ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control"
                        value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" class="form-control"
                        placeholder="09XXXXXXXXX / 639XXXXXXXXX / +639XXXXXXXXX"
                        value="{{ old('emergency_contact_number', $patient->emergency_contact_number ?? '') }}">
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-3 mb-4">
            <h5 class="mb-3">Address</h5>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Region</label>
                    <select name="region" class="form-control" required>
                        <option value="">Select Region</option>
                        <option value="NCR" {{ old('region') === 'NCR' ? 'selected' : '' }}>NCR</option>
                        <option value="Region III" {{ old('region') === 'Region III' ? 'selected' : '' }}>Region III</option>
                        <option value="Region IV-A" {{ old('region') === 'Region IV-A' ? 'selected' : '' }}>Region IV-A</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Province</label>
                    <select name="province" class="form-control" required>
                        <option value="">Select Province</option>
                        <option value="Metro Manila" {{ old('province') === 'Metro Manila' ? 'selected' : '' }}>Metro Manila</option>
                        <option value="Bulacan" {{ old('province') === 'Bulacan' ? 'selected' : '' }}>Bulacan</option>
                        <option value="Laguna" {{ old('province') === 'Laguna' ? 'selected' : '' }}>Laguna</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">City / Municipality</label>
                    <select name="city" class="form-control" required>
                        <option value="">Select City / Municipality</option>
                        <option value="Quezon City" {{ old('city') === 'Quezon City' ? 'selected' : '' }}>Quezon City</option>
                        <option value="Makati" {{ old('city') === 'Makati' ? 'selected' : '' }}>Makati</option>
                        <option value="Calamba" {{ old('city') === 'Calamba' ? 'selected' : '' }}>Calamba</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Barangay</label>
                    <select name="barangay" class="form-control" required>
                        <option value="">Select Barangay</option>
                        <option value="Bagumbayan" {{ old('barangay') === 'Bagumbayan' ? 'selected' : '' }}>Bagumbayan</option>
                        <option value="Commonwealth" {{ old('barangay') === 'Commonwealth' ? 'selected' : '' }}>Commonwealth</option>
                        <option value="San Antonio" {{ old('barangay') === 'San Antonio' ? 'selected' : '' }}>San Antonio</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Street / House No. / Unit</label>
                    <input type="text" name="address_line" class="form-control" value="{{ old('address_line') }}">
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-3 mb-4">
            <h5 class="mb-3">Appointment Details</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dental Service</label>
                    <select name="service_id" id="service_id" class="form-control" required>
                        <option value="">Select Service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->service_id }}"
                                data-description="{{ $service->description }}"
                                data-duration="{{ $service->estimated_duration_minutes }}"
                                data-price="{{ $service->estimated_price }}"
                                {{ (string) old('service_id') === (string) $service->service_id ? 'selected' : '' }}>
                                {{ $service->service_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Preferred Dentist</label>
                    <select name="preferred_dentist_id" id="preferred_dentist_id" class="form-control">
                        <option value="">Any Available Dentist</option>
                        @forelse(($dentists ?? []) as $dentist)
                            <option value="{{ $dentist->dentist_id }}"
                                {{ (string) old('preferred_dentist_id') === (string) $dentist->dentist_id ? 'selected' : '' }}>
                                Dentist #{{ $dentist->dentist_id }}
                            </option>
                        @empty
                            <option value="" disabled>No available dentists found</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="mb-2">Service Information</h6>
                        <div id="serviceMetaDescription">Select a service to view the description.</div>
                        <div class="mt-2">
                            <strong>Estimated Duration:</strong> <span id="serviceMetaDuration">-</span><br>
                            <strong>Estimated Price:</strong> ₱<span id="serviceMetaPrice">-</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Service-Specific Questions</label>
                    <div id="dynamicQuestions" class="border rounded p-3 bg-light">
                        <p class="text-muted mb-0">Select a service to load additional questions.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Date</label>
                    <input type="date" name="preferred_date" id="preferred_date" class="form-control"
                        min="{{ now()->toDateString() }}"
                        value="{{ old('preferred_date') }}"
                        required>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label">Available Time Slots</label>
                    <select name="preferred_start_time" id="preferred_start_time" class="form-control" required>
                        <option value="">Select an available time slot</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes / Concerns</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Review Booking</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const dentistSelect = document.getElementById('preferred_dentist_id');
    const dateInput = document.getElementById('preferred_date');
    const timeSelect = document.getElementById('preferred_start_time');
    const dynamicQuestions = document.getElementById('dynamicQuestions');

    const descriptionEl = document.getElementById('serviceMetaDescription');
    const durationEl = document.getElementById('serviceMetaDuration');
    const priceEl = document.getElementById('serviceMetaPrice');

    function updateServiceMetaFromOption() {
        const option = serviceSelect.options[serviceSelect.selectedIndex];

        if (!option || !option.value) {
            descriptionEl.textContent = 'Select a service to view the description.';
            durationEl.textContent = '-';
            priceEl.textContent = '-';
            return;
        }

        descriptionEl.textContent = option.dataset.description || '-';
        durationEl.textContent = (option.dataset.duration || '-') + ' minutes';
        priceEl.textContent = option.dataset.price || '-';
    }

    async function loadServiceQuestions() {
        const serviceId = serviceSelect.value;

        if (!serviceId) {
            dynamicQuestions.innerHTML = '<p class="text-muted mb-0">Select a service to load additional questions.</p>';
            return;
        }

        try {
            const response = await fetch(`/booking/services/${serviceId}/questions`);
            const questions = await response.json();

            if (!Array.isArray(questions) || questions.length === 0) {
                dynamicQuestions.innerHTML = '<p class="text-muted mb-0">No additional questions for this service.</p>';
                return;
            }

            let html = '';

            questions.forEach((question) => {
                html += `<div class="mb-3">`;
                html += `<label class="form-label"><strong>${question.option_name}</strong>${question.is_required ? ' *' : ''}</label>`;

                const values = Array.isArray(question.values) ? question.values : [];
                const fieldName = `answers[${question.option_id}]`;

                if (['select', 'radio'].includes(question.option_type) && values.length > 0) {
                    html += `<select name="${fieldName}" class="form-control">`;
                    html += `<option value="">Select</option>`;
                    values.forEach((value) => {
                        html += `<option value="${value.value_id}">${value.value_label}</option>`;
                    });
                    html += `</select>`;
                } else if (question.option_type === 'checkbox' && values.length > 0) {
                    values.forEach((value) => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="${fieldName}[]" value="${value.value_id}" id="opt_${question.option_id}_${value.value_id}">
                                <label class="form-check-label" for="opt_${question.option_id}_${value.value_id}">
                                    ${value.value_label}
                                </label>
                            </div>
                        `;
                    });
                } else if (question.option_type === 'textarea') {
                    html += `<textarea name="${fieldName}" class="form-control" rows="3"></textarea>`;
                } else if (question.option_type === 'number') {
                    html += `<input type="number" name="${fieldName}" class="form-control">`;
                } else {
                    html += `<input type="text" name="${fieldName}" class="form-control">`;
                }

                html += `</div>`;
            });

            dynamicQuestions.innerHTML = html;
        } catch (error) {
            dynamicQuestions.innerHTML = '<p class="text-danger mb-0">Failed to load service questions.</p>';
        }
    }

    async function loadAvailableSlots() {
        const serviceId = serviceSelect.value;
        const dentistId = dentistSelect.value;
        const date = dateInput.value;

        timeSelect.innerHTML = '<option value="">Select an available time slot</option>';

        if (!serviceId || !date) {
            return;
        }

        let url = `/booking/available-slots?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;
        if (dentistId) {
            url += `&dentist_id=${encodeURIComponent(dentistId)}`;
        }

        try {
            const response = await fetch(url);
            const slots = await response.json();

            if (!Array.isArray(slots) || slots.length === 0) {
                timeSelect.innerHTML = '<option value="">No available slots</option>';
                return;
            }

            slots.forEach((slot) => {
                const option = document.createElement('option');
                option.value = slot.start_time;
                option.textContent = slot.label;
                timeSelect.appendChild(option);
            });
        } catch (error) {
            timeSelect.innerHTML = '<option value="">Failed to load slots</option>';
        }
    }

    serviceSelect.addEventListener('change', function () {
        updateServiceMetaFromOption();
        loadServiceQuestions();
        loadAvailableSlots();
    });

    dentistSelect.addEventListener('change', loadAvailableSlots);
    dateInput.addEventListener('change', loadAvailableSlots);

    updateServiceMetaFromOption();
    loadServiceQuestions();
    loadAvailableSlots();
});
</script>
@endsection
