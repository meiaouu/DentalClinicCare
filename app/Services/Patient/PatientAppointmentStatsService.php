<?php

namespace App\Services\Patient;

use App\Models\Patient;

class PatientAppointmentStatsService
{
    public function summary(Patient $patient): array
    {
        $requestCounts = $patient->appointmentRequests()
            ->selectRaw('request_status, COUNT(*) as aggregate')
            ->groupBy('request_status')
            ->pluck('aggregate', 'request_status')
            ->toArray();

        $appointmentCounts = $patient->appointments()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        return [
            'total_times_set_appointment' => (int) $patient->appointmentRequests()->count(),
            'total_actual_appointments' => (int) $patient->appointments()->count(),
            'statuses' => [
                'pending' => (int) (($requestCounts['pending'] ?? 0) + ($requestCounts['under_review'] ?? 0)),
                'confirmed' => (int) ($appointmentCounts['confirmed'] ?? 0),
                'checked_in' => (int) ($appointmentCounts['checked_in'] ?? 0),
                'completed' => (int) ($appointmentCounts['completed'] ?? 0),
                'no_show' => (int) ($appointmentCounts['no_show'] ?? 0),
                'cancelled' => (int) ($appointmentCounts['cancelled'] ?? 0),
                'rejected' => (int) ($requestCounts['rejected'] ?? 0),
                'rescheduled' => (int) ($appointmentCounts['rescheduled'] ?? 0),
            ],
        ];
    }
}
