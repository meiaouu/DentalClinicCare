<?php

namespace Database\Seeders;

use App\Models\ClinicSetting;
use Illuminate\Database\Seeder;

class ClinicSettingSeeder extends Seeder
{
    public function run(): void
    {
        ClinicSetting::updateOrCreate(
            ['setting_id' => 1],
            [
                'clinic_name' => 'Dental Clinic Care',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'slot_interval_minutes' => 30,
                'default_no_show_minutes' => 30,
                'allow_patient_cancel_pending' => true,
                'allow_patient_cancel_confirmed' => false,
                'contact_number' => '+63 917 000 0000',
                'clinic_email' => 'clinic@email.com',
                'clinic_location' => 'Quezon City, Philippines',
                'facebook_url' => 'https://facebook.com/',
                'messenger_url' => 'https://m.me/',
                'instagram_url' => 'https://instagram.com/',
            ]
        );
    }
}
