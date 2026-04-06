<?php

namespace Database\Seeders;

use App\Models\ClinicWeeklySchedule;
use Illuminate\Database\Seeder;

class ClinicWeeklyScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $openDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($openDays as $day) {
            ClinicWeeklySchedule::updateOrCreate(
                ['day_of_week' => $day],
                [
                    'is_open' => true,
                    'open_time' => '08:00:00',
                    'close_time' => '18:00:00',
                    'is_reserve_only' => false,
                ]
            );
        }

        ClinicWeeklySchedule::updateOrCreate(
            ['day_of_week' => 'saturday'],
            [
                'is_open' => false,
                'open_time' => null,
                'close_time' => null,
                'is_reserve_only' => false,
            ]
        );

        ClinicWeeklySchedule::updateOrCreate(
            ['day_of_week' => 'sunday'],
            [
                'is_open' => false,
                'open_time' => null,
                'close_time' => null,
                'is_reserve_only' => true,
            ]
        );
    }
}
