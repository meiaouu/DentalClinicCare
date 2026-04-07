<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ConfirmAppointmentRequest;
use App\Http\Requests\Staff\RejectAppointmentRequest;
use App\Http\Requests\Staff\RescheduleAppointmentRequest;
use App\Models\AppointmentRequest;
use App\Models\Dentist;
use App\Services\Appointment\AppointmentRequestReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppointmentRequestController extends Controller
{
    public function __construct(
        protected AppointmentRequestReviewService $reviewService
    ) {
    }

    public function index(): View
    {
        $requests = AppointmentRequest::with([
                'service',
                'preferredDentist.user',
                'patient',
            ])
            ->whereIn('request_status', ['pending', 'under_review'])
            ->latest('created_at')
            ->paginate(15);

        return view('staff.appointment-requests.index', compact('requests'));
    }

    public function show(AppointmentRequest $appointmentRequest): View
    {
        $appointmentRequest->load([
            'service',
            'answers.option.values',
            'preferredDentist.user',
            'patient',
            'convertedAppointment',
        ]);

        $dentists = Dentist::with('user')
            ->where('is_active', true)
            ->get();

        return view('staff.appointment-requests.show', [
            'requestItem' => $appointmentRequest,
            'dentists' => $dentists,
        ]);
    }

    public function confirm(
        ConfirmAppointmentRequest $request,
        AppointmentRequest $appointmentRequest
    ): RedirectResponse {
        try {
            $appointment = $this->reviewService->confirm(
                requestModel: $appointmentRequest,
                dentistId: (int) $request->input('dentist_id'),
                appointmentDate: $request->input('appointment_date'),
                startTime: $request->input('start_time'),
                staffNotes: $request->input('remarks')
            );

            return redirect()
                ->route('staff.appointment-requests.show', $appointmentRequest->request_id)
                ->with('success', 'Request confirmed. Appointment created: ' . $appointment->appointment_code);
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['confirm' => $e->getMessage()])
                ->withInput();
        }
    }

    public function reject(
        RejectAppointmentRequest $request,
        AppointmentRequest $appointmentRequest
    ): RedirectResponse {
        try {
            $this->reviewService->reject(
                requestModel: $appointmentRequest,
                staffNotes: $request->input('remarks')
            );

            return redirect()
                ->route('staff.appointment-requests.index')
                ->with('success', 'Request rejected successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['reject' => $e->getMessage()])
                ->withInput();
        }
    }

    public function reschedule(
        RescheduleAppointmentRequest $request,
        AppointmentRequest $appointmentRequest
    ): RedirectResponse {
        try {
            $this->reviewService->reschedule(
                requestModel: $appointmentRequest,
                dentistId: (int) $request->input('dentist_id'),
                appointmentDate: $request->input('appointment_date'),
                startTime: $request->input('start_time'),
                staffNotes: $request->input('remarks')
            );

            return redirect()
                ->route('staff.appointment-requests.show', $appointmentRequest->request_id)
                ->with('success', 'Request rescheduled successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['reschedule' => $e->getMessage()])
                ->withInput();
        }
    }
}
