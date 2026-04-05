<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'booked_by',
        'confirmed_by',
        'cancelled_by',
        'cancellation_reason',
        'checked_in_at',
        'completed_at',
        'no_show_at',
        'remarks',
    ];

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
}
