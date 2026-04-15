<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStaffAppointmentRequest;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Service;
use App\Services\Appointment\AppointmentStatusService;
use App\Services\Booking\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentStatusService $statusService,
        protected BookingAvailabilityService $availabilityService
    ) {
    }

    public function index(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());
        $status = $request->input('status');

        $appointments = Appointment::query()
            ->with([
                'patient',
                'request',
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

    public function store(StoreStaffAppointmentRequest $request): RedirectResponse
{
    /** @var \App\Models\User|null $staffUser */
    $staffUser = \Illuminate\Support\Facades\Auth::user();

    if (!$staffUser) {
        return back()->withErrors([
            'auth' => 'Authenticated staff user not found.',
        ]);
    }

    $serviceId = (int) $request->input('service_id');
    $patientId = (int) $request->input('patient_id');
    $dentistId = (int) $request->input('dentist_id');
    $appointmentDate = (string) $request->input('appointment_date');
    $startTime = (string) $request->input('start_time');
    $remarks = $request->input('remarks');

    if ($startTime === '') {
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
        return ($slot['start_time'] ?? null) === $start->format('H:i:s');
    });

    if (!$isValidSlot) {
        return back()->withErrors([
            'schedule' => 'Selected time is not available or already booked.',
        ])->withInput();
    }

    $appointment = DB::transaction(function () use (
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
        return Appointment::create([
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
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$this->canMarkArrival($appointment, 15)) {
            return back()->withErrors([
                'appointment' => 'Patient can only be marked as arrived within 15 minutes before the appointment time until the end of the appointment day.',
            ]);
        }

        if (in_array($appointment->status, ['completed', 'cancelled', 'no_show'], true)) {
            return back()->withErrors([
                'appointment' => 'This appointment can no longer be marked as arrived.',
            ]);
        }

        try {
            $appointment->update([
                'arrival_status' => 'arrived',
            ]);

            return back()->with('success', 'Patient marked as arrived.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }
    }

    public function checkIn(Request $request, Appointment $appointment): RedirectResponse
{
    $request->validate([
        'remarks' => ['nullable', 'string', 'max:1000'],
    ]);

    if (!$this->canMarkArrival($appointment, 15)) {
        return back()->withErrors([
            'appointment' => 'Patient can only be checked in from 15 minutes before the appointment time until the end of the appointment day.',
        ]);
    }

    if (in_array($appointment->status, ['completed', 'cancelled', 'no_show'], true)) {
        return back()->withErrors([
            'appointment' => 'This appointment can no longer be checked in.',
        ]);
    }

    try {
        $this->statusService->checkIn($appointment, $request->input('remarks'));

        return back()->with('success', 'Patient checked in successfully.');
    } catch (\Throwable $e) {
        return back()->withErrors([
            'status' => $e->getMessage(),
        ]);
    }
}


    public function markInProgress(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->statusService->markInProgress($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment marked in progress.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }
    }

    public function complete(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->statusService->complete($appointment, $request->input('remarks'));

            return back()->with('success', 'Appointment completed successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }
    }

    public function markNoShow(Request $request, Appointment $appointment): RedirectResponse
{
    $request->validate([
        'remarks' => ['nullable', 'string', 'max:1000'],
    ]);

    if (in_array($appointment->status, ['completed', 'cancelled'], true)) {
        return back()->withErrors([
            'appointment' => 'Completed or cancelled appointments cannot be marked as no show.',
        ]);
    }

    try {
        $this->statusService->markNoShow($appointment, $request->input('remarks'));

        return back()->with('success', 'Appointment marked as no-show.');
    } catch (\Throwable $e) {
        return back()->withErrors([
            'status' => $e->getMessage(),
        ]);
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
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }
    }

    protected function getAppointmentStartDateTime(Appointment $appointment): Carbon
{
    $appointmentDate = $appointment->appointment_date instanceof Carbon
        ? $appointment->appointment_date->format('Y-m-d')
        : Carbon::parse($appointment->appointment_date)->format('Y-m-d');

    return Carbon::parse($appointmentDate . ' ' . $appointment->start_time);
}

protected function getAppointmentEndOfDay(Appointment $appointment): Carbon
{
    $appointmentDate = $appointment->appointment_date instanceof Carbon
        ? $appointment->appointment_date->format('Y-m-d')
        : Carbon::parse($appointment->appointment_date)->format('Y-m-d');

    return Carbon::parse($appointmentDate . ' 23:59:59');
}

    protected function canMarkArrival(Appointment $appointment, int $minutesBefore = 15): bool
{
    if (!$appointment->appointment_date || !$appointment->start_time) {
        return false;
    }

    if (in_array($appointment->status, ['completed', 'cancelled', 'no_show'], true)) {
        return false;
    }

    $startDateTime = $this->getAppointmentStartDateTime($appointment);
    $allowedFrom = $startDateTime->copy()->subMinutes($minutesBefore);
    $endOfDay = $this->getAppointmentEndOfDay($appointment);
    $now = now();

    return $now->greaterThanOrEqualTo($allowedFrom)
        && $now->lessThanOrEqualTo($endOfDay);
}

}
