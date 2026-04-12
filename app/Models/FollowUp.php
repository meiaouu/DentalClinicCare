<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $table = 'follow_ups';

    protected $primaryKey = 'follow_up_id';

    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'dentist_id',
        'treatment_id',
        'recommended_date',
        'reason',
        'remarks',
        'status',
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

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id', 'treatment_id');
    }
}
