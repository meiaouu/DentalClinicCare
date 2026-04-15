<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class MarkAppointmentsNoShow extends Command
{
    protected $signature = 'appointments:mark-no-show';
    protected $description = 'Mark unattended past-day appointments as no show';

    public function handle(): int
    {
        $appointments = Appointment::query()
            ->whereDate('appointment_date', '<', now()->toDateString())
            ->whereIn('status', ['confirmed', 'rescheduled'])
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            $appointment->update([
                'status' => 'no_show',
                'arrival_status' => 'no_show',
            ]);

            $count++;
        }

        $this->info("Marked {$count} appointments as no_show.");

        return self::SUCCESS;
    }
}
