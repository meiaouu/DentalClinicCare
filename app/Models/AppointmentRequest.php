<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentRequest extends Model
{
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_code',
        'patient_id',
        'is_guest',
        'source_channel',
        'guest_first_name',
        'guest_middle_name',
        'guest_last_name',
        'guest_contact_number',
        'guest_email',
        'sex',
        'birth_date',
        'civil_status',
        'address',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_number',
        'preferred_dentist_id',
        'service_id',
        'preferred_date',
        'preferred_start_time',
        'notes',
        'request_status',
    ];

    protected function casts(): array
    {
        return [
            'is_guest' => 'boolean',
            'birth_date' => 'date',
            'preferred_date' => 'date',
        ];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AppointmentRequestAnswer::class, 'request_id', 'request_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function preferredDentist(): BelongsTo
    {
        return $this->belongsTo(Dentist::class, 'preferred_dentist_id', 'dentist_id');
    }
}
