<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'contact_number' => preg_replace('/\s+/', '', (string) $this->contact_number),
            'emergency_contact_number' => preg_replace('/\s+/', '', (string) $this->emergency_contact_number),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sex' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date', 'before:today'],
            'civil_status' => ['required', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:100'],

            'contact_number' => ['required', 'regex:/^(09\d{9}|\+639\d{9}|639\d{9})$/'],
            'email' => ['required', 'email', 'max:150'],

            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'regex:/^(09\d{9}|\+639\d{9}|639\d{9})$/'],

            'region' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'barangay' => ['required', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],

            'service_id' => ['required', 'integer'],
            'preferred_dentist_id' => ['nullable', 'integer'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_start_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'answers' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Contact number must be a valid Philippine mobile number.',
            'emergency_contact_number.regex' => 'Emergency contact number must be a valid Philippine mobile number.',
        ];
    }
}
