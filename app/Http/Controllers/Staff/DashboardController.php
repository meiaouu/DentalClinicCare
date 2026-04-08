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

            'today_confirmed' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'confirmed')
                ->count(),

            'today_checked_in' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'checked_in')
                ->count(),

            'today_in_progress' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'in_progress')
                ->count(),

            'today_completed' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'completed')
                ->count(),

            'today_no_show' => Appointment::query()
                ->whereDate('appointment_date', $today)
                ->where('status', 'no_show')
                ->count(),
        ];

        $todayAppointments = Appointment::query()
            ->with(['patient', 'dentist.user', 'service'])
            ->whereDate('appointment_date', $today)
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        return view('staff.dashboard', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
        ]);
    }
}
