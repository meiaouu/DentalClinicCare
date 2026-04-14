<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $startTime = $this->input('start_time');

        if (is_string($startTime) && strlen($startTime) === 8) {
            $this->merge([
                'start_time' => substr($startTime, 0, 5),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,patient_id'],
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
            'dentist_id' => ['required', 'integer', 'exists:dentists,dentist_id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'service_id.required' => 'Please select a service.',
            'dentist_id.required' => 'Please select a dentist.',
            'appointment_date.required' => 'Please select an appointment date.',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'start_time.required' => 'Please select an available time slot.',
            'start_time.date_format' => 'Please select a valid available time slot.',
        ];
    }
}
