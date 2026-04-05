<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'service_name',
        'description',
        'estimated_duration_minutes',
        'estimated_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'estimated_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(ServiceOption::class, 'service_id', 'service_id');
    }
}
