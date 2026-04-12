<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $table = 'treatments';

    protected $primaryKey = 'treatment_id';

    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'dentist_id',
        'appointment_id',
        'treatment_name',
        'description',
        'treatment_status',
        'notes',
        'treated_at',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id', 'dentist_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'treatment_id', 'treatment_id');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class, 'treatment_id', 'treatment_id');
    }
}
