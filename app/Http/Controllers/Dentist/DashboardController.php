<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Attachment;
use App\Models\Treatment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $dentist = $user->dentist;

        abort_if(!$dentist, 403, 'Dentist profile not found.');

        $today = now()->toDateString();

        $todayAppointments = Appointment::query()
            ->with(['patient', 'service'])
            ->where('dentist_id', $dentist->dentist_id)
            ->whereDate('appointment_date', $today)
            ->orderBy('start_time')
            ->get();

        $pendingFollowUps = 0;
        if (Schema::hasTable('follow_ups')) {
            $pendingFollowUps = \App\Models\FollowUp::query()
                ->where('dentist_id', $dentist->dentist_id)
                ->whereIn('status', ['pending', 'scheduled'])
                ->count();
        }

        $attachmentsCount = 0;
        if (Schema::hasTable('attachments')) {
            $attachmentsCount = Attachment::query()
                ->whereNotNull('patient_id')
                ->count();
        }

        $completedTreatments = 0;
        if (Schema::hasTable('treatments')) {
            $completedTreatments = Treatment::query()
                ->where('dentist_id', $dentist->dentist_id)
                ->where('treatment_status', 'completed')
                ->count();
        }

        $stats = [
            'today_appointments' => $todayAppointments->count(),
            'today_patients' => $todayAppointments->pluck('patient_id')->filter()->unique()->count(),
            'pending_followups' => $pendingFollowUps,
            'attachments_count' => $attachmentsCount,
            'completed_treatments' => $completedTreatments,
        ];

        $upcomingAppointments = Appointment::query()
            ->with(['patient', 'service'])
            ->where('dentist_id', $dentist->dentist_id)
            ->whereDate('appointment_date', '>=', $today)
            ->whereIn('status', ['confirmed', 'checked_in', 'in_progress'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $newVsReturning = [
            'new' => $todayAppointments->whereNull('patient_id')->count(),
            'returning' => $todayAppointments->whereNotNull('patient_id')->count(),
        ];

        return view('dentist.dashboard', compact(
            'dentist',
            'todayAppointments',
            'upcomingAppointments',
            'stats',
            'newVsReturning'
        ));
    }
}
