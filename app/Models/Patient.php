<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'user_id',
        'patient_code',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'birth_date',
        'civil_status',
        'address',
        'occupation',
        'contact_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'notes',
        'profile_status',
        'created_by',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }

    public function appointmentRequests(): HasMany
    {
        return $this->hasMany(AppointmentRequest::class, 'patient_id', 'patient_id');
    }

    public function messageThreads()
{
    return $this->hasMany(\App\Models\MessageThread::class, 'patient_id', 'patient_id');
}
    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])->filter()->implode(' '));
    }

    public function getAppointmentStatusSummaryAttribute(): array
{
    $requestCounts = $this->appointmentRequests()
        ->selectRaw('request_status, COUNT(*) as aggregate')
        ->groupBy('request_status')
        ->pluck('aggregate', 'request_status')
        ->toArray();

    $appointmentCounts = $this->appointments()
        ->selectRaw('status, COUNT(*) as aggregate')
        ->groupBy('status')
        ->pluck('aggregate', 'status')
        ->toArray();

    return [
        'total_times_set_appointment' => (int) $this->appointmentRequests()->count(),
        'total_actual_appointments' => (int) $this->appointments()->count(),
        'statuses' => [
            'pending' => (int) (($requestCounts['pending'] ?? 0) + ($requestCounts['under_review'] ?? 0)),
            'confirmed' => (int) ($appointmentCounts['confirmed'] ?? 0),
            'checked_in' => (int) ($appointmentCounts['checked_in'] ?? 0),
            'completed' => (int) ($appointmentCounts['completed'] ?? 0),
            'no_show' => (int) ($appointmentCounts['no_show'] ?? 0),
            'cancelled' => (int) ($appointmentCounts['cancelled'] ?? 0),
            'rejected' => (int) ($requestCounts['rejected'] ?? 0),
            'rescheduled' => (int) ($appointmentCounts['rescheduled'] ?? 0),
        ],
    ];
}

}
