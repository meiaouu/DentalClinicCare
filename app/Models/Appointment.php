<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'appointment_code',
        'request_id',
        'dentist_id',
        'patient_id',
        'service_id',
        'appointment_date',
        'start_time',
        'end_time',
        'estimated_duration_minutes',
        'estimated_price',
        'status',
        'grace_period_minutes',
        'queue_number',
        'arrival_status',
        'booked_by',
        'confirmed_by',
        'cancelled_by',
        'cancellation_reason',
        'checked_in_at',
        'completed_at',
        'no_show_at',
        'remarks',
    ];

     protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'estimated_duration_minutes' => 'integer',
            'estimated_price' => 'decimal:2',
            'grace_period_minutes' => 'integer',
            'queue_number' => 'integer',
        ];
    }

     public function request(): BelongsTo
    {
        return $this->belongsTo(AppointmentRequest::class, 'request_id', 'request_id');
    }

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id', 'dentist_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(AppointmentStatusLog::class, 'appointment_id', 'appointment_id');
    }


}
