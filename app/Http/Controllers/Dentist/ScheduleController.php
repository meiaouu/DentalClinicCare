<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $dentist = $user?->dentist;

        abort_if(!$dentist, 403, 'Dentist account not found.');

        $appointments = Appointment::query()
            ->with([
                'patient',
                'service',
                'dentist.user',
                'request',
            ])
            ->where('dentist_id', $dentist->dentist_id)
            ->whereIn('status', ['confirmed', 'checked_in', 'rescheduled', 'completed'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        $groupedAppointments = $appointments
            ->groupBy(function (Appointment $appointment) {
                return Carbon::parse($appointment->appointment_date)->format('Y-m-d');
            })
            ->map(function (Collection $items, string $date) {
                $dateCarbon = Carbon::parse($date)->startOfDay();
                $today = now()->startOfDay();
                $tomorrow = now()->copy()->addDay()->startOfDay();

                $label = $dateCarbon->format('F d, Y');

                if ($dateCarbon->equalTo($today)) {
                    $label = 'Today';
                } elseif ($dateCarbon->equalTo($tomorrow)) {
                    $label = 'Tomorrow';
                }

                $mappedItems = $items->map(function (Appointment $appointment) {
                    $appointmentDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');

                    $start = Carbon::parse($appointmentDate . ' ' . $appointment->start_time);
                    $end = Carbon::parse($appointmentDate . ' ' . $appointment->end_time);

                    $patientName = trim(
                        ($appointment->patient?->first_name ?? '') . ' ' .
                        ($appointment->patient?->middle_name ?? '') . ' ' .
                        ($appointment->patient?->last_name ?? '')
                    );

                    if ($patientName === '') {
                        $patientName = trim(
                            ($appointment->request?->guest_first_name ?? '') . ' ' .
                            ($appointment->request?->guest_middle_name ?? '') . ' ' .
                            ($appointment->request?->guest_last_name ?? '')
                        );
                    }

                    $isReturning = false;

                    if ($appointment->patient) {
                        $isReturning = Appointment::query()
                            ->where('patient_id', $appointment->patient->patient_id)
                            ->where('appointment_id', '!=', $appointment->appointment_id)
                            ->whereIn('status', ['confirmed', 'checked_in', 'rescheduled', 'completed'])
                            ->exists();
                    }

                    return [
                        'appointment' => $appointment,
                        'start' => $start,
                        'end' => $end,
                        'is_past' => $end->lt(now()),
                        'patient_name' => $patientName !== '' ? $patientName : 'Patient not available',
                        'is_returning' => $isReturning,
                    ];
                })->values();

                return [
                    'date' => $date,
                    'label' => $label,
                    'is_today' => $dateCarbon->equalTo($today),
                    'items' => $mappedItems,
                ];
            })
            ->values();

        $todayCount = $appointments->filter(function ($appointment) {
            return Carbon::parse($appointment->appointment_date)->isToday();
        })->count();

        $upcomingCount = $appointments->filter(function ($appointment) {
            return Carbon::parse($appointment->appointment_date)->greaterThanOrEqualTo(now()->startOfDay());
        })->count();

        $completedCount = $appointments->where('status', 'completed')->count();

        return view('dentist.schedule.index', [
            'dentist' => $dentist,
            'appointments' => $appointments,
            'groupedAppointments' => $groupedAppointments,
            'todayCount' => $todayCount,
            'upcomingCount' => $upcomingCount,
            'completedCount' => $completedCount,
        ]);
    }
}
