<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $stats = [
            'pending_requests' => AppointmentRequest::query()
                ->whereIn('request_status', ['pending', 'under_review'])
                ->count(),

            'today_appointments' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->count(),

            'confirmed_upcoming' => Appointment::query()
                ->whereDate('appointment_date', '>', $today)
                ->where('status', 'confirmed')
                ->count(),

            'checked_in_today' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'checked_in')
                ->count(),

            'completed_today' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'completed')
                ->count(),

            'no_show_today' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'no_show')
                ->count(),
        ];

        $todayAppointments = Appointment::query()
            ->with([
                'patient',
                'dentist.user',
                'service',
                'request',
            ])
            ->whereDate('appointment_date', $today)
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $pendingRequests = AppointmentRequest::query()
            ->with([
                'patient',
                'service',
                'preferredDentist.user',
            ])
            ->whereIn('request_status', ['pending', 'under_review'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        $upcomingAppointments = Appointment::query()
            ->with([
                'patient',
                'dentist.user',
                'service',
                'request',
            ])
            ->whereDate('appointment_date', '>', $today)
            ->whereIn('status', ['confirmed', 'rescheduled'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        return view('staff.dashboard', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'pendingRequests' => $pendingRequests,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
