<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'contact_number' => preg_replace('/\s+/', '', (string) $this->input('contact_number')),
            'emergency_contact_number' => preg_replace('/\s+/', '', (string) $this->input('emergency_contact_number')),
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
            'civil_status' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'contact_number' => ['required', 'regex:/^(09\d{9}|\+639\d{9}|639\d{9})$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'regex:/^(09\d{9}|\+639\d{9}|639\d{9})$/'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Use a valid Philippine mobile number.',
            'emergency_contact_number.regex' => 'Use a valid Philippine mobile number.',
        ];
    }
}
