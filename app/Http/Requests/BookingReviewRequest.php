<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Services\Booking\PhoneNumberService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookingReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
            'preferred_dentist_id' => ['nullable', 'integer', 'exists:dentists,dentist_id'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_start_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'region' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'barangay' => ['required', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sex' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date', 'before:today'],
            'civil_status' => ['required', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:100'],

            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],

            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'preferred_date.after_or_equal' => 'The appointment date must not be in the past.',
            'preferred_start_time.date_format' => 'The appointment time must be in valid 24-hour format.',
            'contact_number.required' => 'Contact number is required.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $phoneService = app(PhoneNumberService::class);

            try {
                if ($this->filled('contact_number')) {
                    $phoneService->normalizePhilippineMobile((string) $this->input('contact_number'));
                }

                if ($this->filled('emergency_contact_number')) {
                    $phoneService->normalizePhilippineMobile((string) $this->input('emergency_contact_number'));
                }
            } catch (\InvalidArgumentException $e) {
                $validator->errors()->add('contact_number', $e->getMessage());
            }

            $serviceId = (int) $this->input('service_id');
            $service = Service::query()->find($serviceId);

            if (!$service || (int) $service->is_active !== 1) {
                $validator->errors()->add('service_id', 'Selected service is not available.');
            }
        });
    }
}
