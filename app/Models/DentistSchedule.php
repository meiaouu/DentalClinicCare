<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentistSchedule extends Model
{
    protected $table = 'dentist_schedules';
    protected $primaryKey = 'dentist_schedule_id';

    protected $fillable = [
        'dentist_id',
        'day_of_week',
        'is_available',
        'start_time',
        'end_time',
    ];
}
