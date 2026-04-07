<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dentist_id' => ['required', 'exists:dentists,dentist_id'],
            'preferred_date' => ['required', 'date'],
            'preferred_start_time' => ['required', 'date_format:H:i'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
