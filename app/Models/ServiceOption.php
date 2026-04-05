<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOption extends Model
{
    protected $primaryKey = 'option_id';

    protected $fillable = [
        'service_id',
        'option_name',
        'option_type',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ServiceOptionValue::class, 'option_id', 'option_id')->orderBy('sort_order');
    }
}
