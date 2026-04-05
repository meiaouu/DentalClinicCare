<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'user_id',
        'patient_code',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'birth_date',
        'civil_status',
        'address',
        'occupation',
        'contact_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'notes',
        'profile_status',
        'created_by',
        'is_guest_converted',
        'converted_from_request_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_guest_converted' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
