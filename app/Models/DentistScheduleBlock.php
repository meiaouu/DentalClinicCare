<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentistScheduleBlock extends Model
{
    protected $table = 'dentist_schedule_blocks';
    protected $primaryKey = 'block_id';
    public $timestamps = false;

    protected $fillable = [
        'dentist_id',
        'block_date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id', 'dentist_id');
    }
}
