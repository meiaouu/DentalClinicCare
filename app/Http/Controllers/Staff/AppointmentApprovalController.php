<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ApproveAppointmentRequest;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\Request;

class AppointmentApprovalController extends Controller
{
    public function approve(ApproveAppointmentRequest $request, AppointmentService $appointmentService)
    {
        $validated = $request->validated();

        $appointmentService->approveRequest(
            (int) $validated['appointment_request_id'],
            isset($validated['dentist_id']) ? (int) $validated['dentist_id'] : null,
            $validated['appointment_date'],
            $validated['start_time']
        );

        return back()->with('success', 'Appointment request approved successfully.');
    }

    public function reject(Request $request, AppointmentService $appointmentService)
    {
        $validated = $request->validate([
            'appointment_request_id' => ['required', 'integer'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointmentService->rejectRequest(
            (int) $validated['appointment_request_id'],
            $validated['reason'] ?? null
        );

        return back()->with('success', 'Appointment request rejected successfully.');
    }
}
