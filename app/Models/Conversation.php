<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $primaryKey = 'conversation_id';

    protected $fillable = [
        'patient_id',
        'appointment_request_id',
        'handled_by',
        'conversation_status',
        'is_guest',
        'guest_name',
        'guest_contact_number',
        'last_message_at',
    ];

    protected $casts = [
        'is_guest' => 'boolean',
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

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id', 'conversation_id')
            ->orderBy('message_id');
    }

    public function latestMessage(): HasMany
{
    return $this->hasMany(Message::class, 'conversation_id', 'conversation_id')
        ->latest('message_id')
        ->limit(1);
}

    public function unreadMessages(): HasMany
{
    return $this->hasMany(Message::class, 'conversation_id', 'conversation_id')
        ->whereNull('read_at');
}
}
