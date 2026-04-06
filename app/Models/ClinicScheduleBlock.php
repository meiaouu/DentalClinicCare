<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicScheduleBlock extends Model
{
    protected $table = 'clinic_schedule_blocks';
    protected $primaryKey = 'block_id';
    public $timestamps = false;

    protected $fillable = [
        'block_date',
        'start_time',
        'end_time',
        'is_full_day',
        'reason',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'is_full_day' => 'boolean',
    ];
}
