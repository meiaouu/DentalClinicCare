<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicWeeklySchedule extends Model
{
    protected $table = 'clinic_weekly_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
        'is_reserve_only',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'is_reserve_only' => 'boolean',
    ];
}
