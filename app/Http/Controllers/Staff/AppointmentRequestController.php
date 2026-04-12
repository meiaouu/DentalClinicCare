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

class AppointmentRequestController extends Controller
{
    public function __construct(
        protected AppointmentRequestReviewService $reviewService
    ) {
    }

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $date = $request->input('date');
        $patient = $request->input('patient');
        $dentistId = $request->input('dentist_id');

        $requests = AppointmentRequest::query()
            ->with([
                'service',
                'preferredDentist.user',
                'patient',
            ])
            ->when($status, fn ($query) => $query->where('request_status', $status))
            ->when($date, fn ($query) => $query->whereDate('preferred_date', $date))
            ->when($dentistId, fn ($query) => $query->where('preferred_dentist_id', $dentistId))
            ->when($patient, function ($query) use ($patient) {
                $query->where(function ($subQuery) use ($patient) {
                    $subQuery->whereHas('patient', function ($patientQuery) use ($patient) {
                        $patientQuery->where('first_name', 'like', "%{$patient}%")
                            ->orWhere('last_name', 'like', "%{$patient}%");
                    })->orWhere('guest_first_name', 'like', "%{$patient}%")
                      ->orWhere('guest_last_name', 'like', "%{$patient}%");
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $dentists = Dentist::with('user')
            ->where('is_active', true)
            ->get();

        return view('staff.appointment-requests.index', [
            'requests' => $requests,
            'dentists' => $dentists,
            'filters' => compact('status', 'date', 'patient', 'dentistId'),
        ]);
    }

    public function show(AppointmentRequest $appointmentRequest): View
    {
        $appointmentRequest->load([
            'service',
            'answers.option.values',
            'preferredDentist.user',
            'patient',
            'convertedAppointment.statusLogs',
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
