<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dentist extends Model
{
    protected $primaryKey = 'dentist_id';

    protected $fillable = [
        'user_id',
        'dentist_code',
        'license_number',
        'specialization',
        'is_owner',
        'consultation_fee',
        'bio',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_owner' => 'boolean',
            'is_active' => 'boolean',
            'consultation_fee' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function dateOverrides()
{
    return $this->hasMany(\App\Models\DentistDateOverride::class, 'dentist_id', 'dentist_id');
}
}
