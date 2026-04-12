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
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AppointmentRequestReviewController extends Controller
{
    public function __construct(
        protected AppointmentRequestReviewService $reviewService
    ) {
    }

    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        $requests = AppointmentRequest::with([
                'service',
                'patient',
                'preferredDentist.user',
                'convertedAppointment',
            ])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('request_status', $status);
            })
            ->latest('request_id')
            ->paginate(15)
            ->withQueryString();

        return view('staff.appointment-requests.index', [
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    public function show(int $requestId): View
    {
        $appointmentRequest = AppointmentRequest::with([
            'service.options.values',
            'answers.option.values',
            'patient',
            'preferredDentist.user',
            'convertedAppointment',
            'statusLogs',
        ])->findOrFail($requestId);

        $dentists = Dentist::with('user')
            ->where('is_active', true)
            ->orderBy('dentist_id')
            ->get();

        return view('staff.appointment-requests.show', [
            'appointmentRequest' => $appointmentRequest,
            'dentists' => $dentists,
        ]);
    }

    public function confirm(ConfirmAppointmentRequest $request, int $requestId): RedirectResponse
    {
        $appointmentRequest = AppointmentRequest::findOrFail($requestId);

        try {
            $appointment = $this->reviewService->confirm(
                requestModel: $appointmentRequest,
                dentistId: (int) $request->input('dentist_id'),
                appointmentDate: $request->input('appointment_date'),
                startTime: $request->input('start_time'),
                staffNotes: $request->input('staff_notes')
            );

            return redirect()
                ->route('staff.appointment-requests.show', $appointmentRequest->request_id)
                ->with('success', 'Request confirmed and appointment #' . $appointment->appointment_id . ' created.');
        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['confirm' => $e->getMessage()])
                ->withInput();
        }
    }

    public function reject(RejectAppointmentRequest $request, int $requestId): RedirectResponse
    {
        $appointmentRequest = AppointmentRequest::findOrFail($requestId);

        try {
            $this->reviewService->reject(
                requestModel: $appointmentRequest,
                staffNotes: $request->input('staff_notes')
            );

            return redirect()
                ->route('staff.appointment-requests.show', $appointmentRequest->request_id)
                ->with('success', 'Request rejected successfully.');
        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['reject' => $e->getMessage()])
                ->withInput();
        }
    }

    public function reschedule(RescheduleAppointmentRequest $request, int $requestId): RedirectResponse
    {
        $appointmentRequest = AppointmentRequest::findOrFail($requestId);

        try {
            $this->reviewService->reschedule(
                requestModel: $appointmentRequest,
                dentistId: (int) $request->input('dentist_id'),
                appointmentDate: $request->input('appointment_date'),
                startTime: $request->input('start_time'),
                staffNotes: $request->input('staff_notes')
            );

            return redirect()
                ->route('staff.appointment-requests.show', $appointmentRequest->request_id)
                ->with('success', 'Request rescheduled successfully.');
        } catch (RuntimeException $e) {
            return back()
                ->withErrors(['reschedule' => $e->getMessage()])
                ->withInput();
        }
    }
}
