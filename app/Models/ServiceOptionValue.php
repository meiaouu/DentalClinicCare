<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOptionValue extends Model
{
    protected $primaryKey = 'value_id';

    protected $fillable = [
        'option_id',
        'value_label',
        'value_code',
        'sort_order',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ServiceOption::class, 'option_id', 'option_id');
    }
}
