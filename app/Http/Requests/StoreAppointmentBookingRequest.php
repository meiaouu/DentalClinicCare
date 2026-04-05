<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_mode' => ['required', Rule::in(['guest', 'account'])],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sex' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:100'],

            'contact_number' => [
                'required',
                'string',
                'regex:/^(09\d{9}|639\d{9}|\+639\d{9})$/',
            ],
            'email' => ['nullable', 'email', 'max:255'],

            'region_id' => ['required'],
            'province_id' => ['required'],
            'city_id' => ['required'],
            'barangay_id' => ['required'],
            'region_name' => ['required', 'string', 'max:150'],
            'province_name' => ['required', 'string', 'max:150'],
            'city_name' => ['required', 'string', 'max:150'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'street' => ['nullable', 'string', 'max:255'],

            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => [
                'nullable',
                'string',
                'regex:/^(09\d{9}|639\d{9}|\+639\d{9})$/',
            ],
            'notes' => ['nullable', 'string'],

            'service_id' => ['required', 'exists:services,service_id'],
            'preferred_dentist_id' => ['nullable', 'exists:dentists,dentist_id'],
            'preferred_date' => ['required', 'date'],
            'preferred_start_time' => ['required', 'date_format:H:i'],
            'service_answers' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $serviceId = $this->input('service_id');

            if (!$serviceId) {
                return;
            }

            $service = Service::with('options.values')->find($serviceId);

            if (!$service || !$service->is_active) {
                $validator->errors()->add('service_id', 'Selected service is not available.');
                return;
            }

            $answers = $this->input('service_answers', []);

            foreach ($service->options as $option) {
                $value = $answers[$option->option_id] ?? null;

                if ($option->is_required && ($value === null || $value === '' || $value === [])) {
                    $validator->errors()->add(
                        "service_answers.{$option->option_id}",
                        "{$option->option_name} is required."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Use a valid Philippine mobile number like 09XXXXXXXXX, 639XXXXXXXXX, or +639XXXXXXXXX.',
            'emergency_contact_number.regex' => 'Use a valid Philippine mobile number like 09XXXXXXXXX, 639XXXXXXXXX, or +639XXXXXXXXX.',
            'preferred_start_time.date_format' => 'Please select a valid time slot.',
            'region_id.required' => 'Please select a region.',
            'province_id.required' => 'Please select a province.',
            'city_id.required' => 'Please select a city or municipality.',
            'barangay_id.required' => 'Please select a barangay.',
        ];
    }
}
