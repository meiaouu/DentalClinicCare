<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class DentistDateOverride extends Model
{
    protected $table = 'dentist_date_overrides';

    protected $primaryKey = 'override_id';

    protected $fillable = [
        'dentist_id',
        'override_date',
        'start_time',
        'end_time',
        'is_available',
        'reason',
    ];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id', 'dentist_id');
    }
}
