<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Appointment\AppointmentStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\Staff\StoreStaffAppointmentRequest;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Service;
use App\Services\Appointment\StaffAppointmentCreationService;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentStatusService $statusService
    ) {
    }

    public function index(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());
        $status = $request->input('status');

        $appointments = Appointment::with([
                'patient',
                'dentist.user',
                'service',
            ])
            ->whereDate('appointment_date', $date)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('staff.appointments.index', compact('appointments', 'date', 'status'));
    }

    public function show(Appointment $appointment): View
{
    $appointment->load([
        'patient',
        'dentist.user',
        'service',
        'request',
        'statusLogs',
    ]);

    $patientSummary = null;

    if ($appointment->patient) {
        $patientSummary = $appointment->patient->appointment_status_summary;
    }

    return view('staff.appointments.show', compact('appointment', 'patientSummary'));
}

    public function markArrived(Request $request, Appointment $appointment): RedirectResponse
    {
        try {
            $this->statusService->markArrived($appointment, $request->input('remarks'));

            return back()->with('success', 'Patient marked as arrived.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function checkIn(Request $request, Appointment $appointment): RedirectResponse
    {
        try {
            $this->statusService->checkIn($appointment, $request->input('remarks'));

            return back()->with('success', 'Patient checked in successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function markInProgress(Request $request, Appointment $appointment): RedirectResponse
    {
        try {
            $this->statusService->markInProgress($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment marked in progress.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function complete(Request $request, Appointment $appointment): RedirectResponse
    {
        try {
            $this->statusService->complete($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment completed successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function markNoShow(Request $request, Appointment $appointment): RedirectResponse
    {
        try {
            $this->statusService->markNoShow($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment marked as no-show.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->statusService->cancel($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment cancelled successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

public function create()
{
    $patients = \App\Models\Patient::query()
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    $services = \App\Models\Service::query()
        ->where('is_active', 1)
        ->orderBy('service_name')
        ->get();

    $dentists = \App\Models\Dentist::query()
        ->with('user')
        ->where('is_active', 1)
        ->get();

    return view('staff.appointments.create', compact('patients', 'services', 'dentists'));
}

public function store(
    StoreStaffAppointmentRequest $request,
    StaffAppointmentCreationService $creationService
): RedirectResponse {
    try {
        $appointment = $creationService->createDirectAppointment(
            patientId: (int) $request->input('patient_id'),
            serviceId: (int) $request->input('service_id'),
            dentistId: (int) $request->input('dentist_id'),
            appointmentDate: $request->input('appointment_date'),
            startTime: $request->input('start_time'),
            remarks: $request->input('remarks')
        );

        return redirect()
            ->route('staff.appointments.show', $appointment->appointment_id)
            ->with('success', 'Appointment created successfully.');
    } catch (\Throwable $e) {
        return back()
            ->withErrors(['appointment' => $e->getMessage()])
            ->withInput();
    }
}
}
