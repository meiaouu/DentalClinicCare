<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'clinic_name',
        'open_time',
        'close_time',
        'slot_interval_minutes',
        'default_no_show_minutes',
        'allow_patient_cancel_pending',
        'allow_patient_cancel_confirmed',
        'contact_number',
        'clinic_email',
        'clinic_location',
        'facebook_url',
        'messenger_url',
        'instagram_url',
    ];

    protected function casts(): array
    {
        return [
            'allow_patient_cancel_pending' => 'boolean',
            'allow_patient_cancel_confirmed' => 'boolean',
            'slot_interval_minutes' => 'integer',
            'default_no_show_minutes' => 'integer',
        ];
    }
}
