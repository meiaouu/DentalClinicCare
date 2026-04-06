<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // replace with policy/role check later
    }

    public function rules(): array
    {
        return [
            'appointment_request_id' => ['required', 'integer'],
            'dentist_id' => ['nullable', 'integer'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s'],
        ];
    }
}
