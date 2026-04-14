<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageThread extends Model
{
    protected $primaryKey = 'thread_id';

    protected $fillable = [
        'patient_id',
        'appointment_request_id',
        'thread_type',
        'subject',
        'last_message_by_user_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function appointmentRequest(): BelongsTo
    {
        return $this->belongsTo(AppointmentRequest::class, 'appointment_request_id', 'request_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id', 'thread_id');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id', 'thread_id')->latest('message_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->patient) {
            return trim(
                ($this->patient->first_name ?? '') . ' ' .
                ($this->patient->last_name ?? '')
            ) ?: 'Patient';
        }

        if ($this->appointmentRequest) {
            return trim(
                ($this->appointmentRequest->guest_first_name ?? '') . ' ' .
                ($this->appointmentRequest->guest_last_name ?? '')
            ) ?: 'Guest Request';
        }

        return 'Message Thread';
    }
}
