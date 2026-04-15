<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\FollowUp;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $patient = $user?->patient;

        $patientId = $patient?->patient_id;

        $upcomingAppointment = null;
        $recentAppointments = collect();
        $recentRequests = collect();
        $followUps = collect();

        $stats = [
            'pending_requests' => 0,
            'upcoming_appointments' => 0,
            'completed_appointments' => 0,
            'follow_ups' => 0,
        ];

        if ($patientId) {
            $upcomingAppointment = Appointment::query()
                ->with(['service', 'dentist.user'])
                ->where('patient_id', $patientId)
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['confirmed', 'rescheduled', 'checked_in'])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->first();

            $recentAppointments = Appointment::query()
                ->with(['service', 'dentist.user'])
                ->where('patient_id', $patientId)
                ->latest('appointment_date')
                ->limit(5)
                ->get();

            $recentRequests = AppointmentRequest::query()
                ->with(['service'])
                ->where('patient_id', $patientId)
                ->latest('created_at')
                ->limit(5)
                ->get();

            $followUps = FollowUp::query()
                ->where('patient_id', $patientId)
                ->orderBy('recommended_date')
                ->limit(5)
                ->get();

            $stats = [
                'pending_requests' => AppointmentRequest::query()
                    ->where('patient_id', $patientId)
                    ->whereIn('request_status', ['pending', 'under_review'])
                    ->count(),

                'upcoming_appointments' => Appointment::query()
                    ->where('patient_id', $patientId)
                    ->whereDate('appointment_date', '>=', now()->toDateString())
                    ->whereIn('status', ['confirmed', 'rescheduled', 'checked_in'])
                    ->count(),

                'completed_appointments' => Appointment::query()
                    ->where('patient_id', $patientId)
                    ->where('status', 'completed')
                    ->count(),

                'follow_ups' => FollowUp::query()
                    ->where('patient_id', $patientId)
                    ->count(),
            ];
        }

        return view('patient.dashboard', [
            'user' => $user,
            'patient' => $patient,
            'stats' => $stats,
            'upcomingAppointment' => $upcomingAppointment,
            'recentAppointments' => $recentAppointments,
            'recentRequests' => $recentRequests,
            'followUps' => $followUps,
        ]);
    }
}
