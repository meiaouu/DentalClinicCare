<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentistUnavailableDate extends Model
{
    protected $table = 'dentist_unavailable_dates';
    protected $primaryKey = 'unavailable_id';
    public $timestamps = false;

    protected $fillable = [
        'dentist_id',
        'unavailable_date',
        'start_time',
        'end_time',
        'reason',
        'created_at',
    ];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id', 'dentist_id');
    }
}
