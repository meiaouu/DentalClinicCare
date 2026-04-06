<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleBlock extends Model
{
    protected $table = 'schedule_blocks';
    protected $primaryKey = 'block_id';

    protected $fillable = [
        'scope',
        'dentist_id',
        'block_date',
        'start_time',
        'end_time',
        'is_full_day',
        'reason',
    ];
}
