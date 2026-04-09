<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $dentist = $user?->dentist;

        abort_unless($dentist, 403, 'Dentist profile not found.');

        $todayAppointments = Appointment::query()
            ->with(['patient', 'service'])
            ->where('dentist_id', $dentist->dentist_id)
            ->whereDate('appointment_date', now()->toDateString())
            ->whereIn('status', ['confirmed', 'rescheduled', 'checked_in', 'in_progress'])
            ->orderBy('start_time')
            ->get();

        $upcomingAppointments = Appointment::query()
            ->with(['patient', 'service'])
            ->where('dentist_id', $dentist->dentist_id)
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['confirmed', 'rescheduled', 'checked_in', 'in_progress'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        return view('dentist.dashboard', [
            'dentist' => $dentist,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
