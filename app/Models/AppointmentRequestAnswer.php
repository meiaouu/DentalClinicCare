<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequestAnswer extends Model
{
    protected $primaryKey = 'request_answer_id';

    protected $fillable = [
        'request_id',
        'option_id',
        'selected_value_id',
        'answer_text',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AppointmentRequest::class, 'request_id', 'request_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ServiceOption::class, 'option_id', 'option_id');
    }

    public function selectedValue(): BelongsTo
    {
        return $this->belongsTo(ServiceOptionValue::class, 'selected_value_id', 'value_id');
    }
}
