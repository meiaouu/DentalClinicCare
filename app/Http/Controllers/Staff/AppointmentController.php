<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Dentist;
use App\Services\Appointment\AppointmentStatusService;
use App\Http\Requests\Staff\StoreStaffAppointmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Booking\BookingAvailabilityService;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{

public function availableSlots(Request $request): JsonResponse
{
    $validated = $request->validate([
        'date' => ['required', 'date'],
        'service_id' => ['required', 'integer'],
        'dentist_id' => ['required', 'integer'],
    ]);

    $slots = $this->availabilityService->getAvailableSlots(
        $validated['date'],
        (int) $validated['service_id'],
        (int) $validated['dentist_id']
    );

    return response()->json([
        'available_slots' => $slots,
    ]);
}

public function create(): View
{
    $patients = Patient::query()
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    $services = Service::query()
        ->where('is_active', 1)
        ->orderBy('service_name')
        ->get();

    $dentists = Dentist::query()
        ->with('user')
        ->where('is_active', 1)
        ->get();

    return view('staff.appointments.create', compact('patients', 'services', 'dentists'));
}



    public function __construct(
    protected AppointmentStatusService $statusService,
    protected \App\Services\Booking\BookingAvailabilityService $availabilityService
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



public function store(StoreStaffAppointmentRequest $request): RedirectResponse
{
    $staffUser = auth()->user();

    if (!$staffUser) {
        return back()->withErrors([
            'auth' => 'Authenticated staff user not found.',
        ]);
    }

    $serviceId = (int) $request->input('service_id');
    $patientId = (int) $request->input('patient_id');
    $dentistId = (int) $request->input('dentist_id');
    $appointmentDate = $request->input('appointment_date');
    $startTime = $request->input('start_time');
    $remarks = $request->input('remarks');

    if (!$startTime) {
        return back()->withErrors([
            'start_time' => 'Please select an available time slot.',
        ])->withInput();
    }

    $service = Service::query()->findOrFail($serviceId);

    $start = Carbon::parse($appointmentDate . ' ' . $startTime);
    $duration = max(1, (int) ($service->estimated_duration_minutes ?? 30));
    $end = $start->copy()->addMinutes($duration);

    $availableSlots = $this->availabilityService->getAvailableSlots(
        $appointmentDate,
        $serviceId,
        $dentistId
    );

    $isValidSlot = collect($availableSlots)->contains(function ($slot) use ($start) {
        return $slot['start_time'] === $start->format('H:i:s');
    });

    if (!$isValidSlot) {
        return back()->withErrors([
            'schedule' => 'Selected time is not available or already booked.',
        ])->withInput();
    }

    DB::transaction(function () use (
        $staffUser,
        $service,
        $serviceId,
        $patientId,
        $dentistId,
        $appointmentDate,
        $start,
        $end,
        $duration,
        $remarks
    ) {
        Appointment::create([
            'appointment_code' => 'APT-' . now()->format('YmdHis'),
            'request_id' => null,
            'patient_id' => $patientId,
            'dentist_id' => $dentistId,
            'service_id' => $serviceId,
            'appointment_date' => $appointmentDate,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'estimated_duration_minutes' => $duration,
            'estimated_price' => $service->estimated_price ?? 0,
            'status' => 'confirmed',
            'arrival_status' => 'pending',
            'grace_period_minutes' => 30,
            'booked_by' => $staffUser->user_id,
            'confirmed_by' => $staffUser->user_id,
            'remarks' => filled($remarks) ? $remarks : null,
        ]);
    });

    return redirect()
        ->route('staff.appointments.index')
        ->with('success', 'Appointment created successfully.');
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
}
