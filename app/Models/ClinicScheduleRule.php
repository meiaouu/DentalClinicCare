<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicScheduleRule extends Model
{
    protected $table = 'clinic_schedule_rules';
    protected $primaryKey = 'rule_id';

    protected $fillable = [
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
    ];
}
