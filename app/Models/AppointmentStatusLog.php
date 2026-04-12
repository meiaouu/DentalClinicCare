<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentStatusLog extends Model
{
    protected $table = 'appointment_status_logs';
    protected $primaryKey = 'status_log_id';

    public $timestamps = false;

    protected $fillable = [
        'appointment_id',
        'old_status',
        'new_status',
        'changed_by',
        'remarks',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }
}
