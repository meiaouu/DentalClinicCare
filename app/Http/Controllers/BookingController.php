<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingReviewRequest;
use App\Models\AppointmentRequest;
use App\Models\AppointmentRequestAnswer;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Models\User;
use App\Notifications\NewAppointmentRequestNotification;
use App\Services\Booking\BookingAvailabilityService;
use App\Services\Booking\PhoneNumberService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    protected PhoneNumberService $phoneNumberService;
    protected BookingAvailabilityService $availabilityService;

    public function __construct(
        PhoneNumberService $phoneNumberService,
        BookingAvailabilityService $availabilityService
    ) {
        $this->phoneNumberService = $phoneNumberService;
        $this->availabilityService = $availabilityService;
    }

    public function entry(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('booking.create');
        }

        return view('public.booking.entry');
    }

    public function guestForm(Request $request): View|RedirectResponse
    {
        $contact = $request->query('contact_number');

        if (!$contact) {
            return redirect()
                ->route('booking.entry')
                ->withErrors(['contact_number' => 'Mobile number is required']);
        }

        try {
            $normalized = $this->phoneNumberService->normalizePhilippineMobile($contact);
        } catch (Throwable $e) {
            return redirect()
                ->route('booking.entry')
                ->withErrors(['contact_number' => $e->getMessage()]);
        }

        $services = Service::query()
            ->where('is_active', 1)
            ->orderBy('service_name')
            ->get();

        return view('public.booking.form', [
            'services' => $services,
            'isGuest' => true,
            'patient' => null,
            'prefillContact' => $normalized,
        ]);
    }

    public function create(): View
    {
        $services = Service::query()
            ->where('is_active', 1)
            ->orderBy('service_name')
            ->get();

        $patient = optional(Auth::user())->patient;

        return view('public.booking.form', [
            'services' => $services,
            'isGuest' => false,
            'patient' => $patient,
            'prefillContact' => null,
        ]);
    }

    public function review(BookingReviewRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();

        try {
            $validated = $this->normalizePhoneFields($validated);

            $service = Service::query()->findOrFail($validated['service_id']);

            // Public booking no longer allows patient/guest dentist selection.
            $validated['preferred_dentist_id'] = null;

            $preferredStartTime = $this->normalizeTimeValue($validated['preferred_start_time']);
            $preferredEndTime = $this->computeEndTime(
                $validated['preferred_date'],
                $preferredStartTime,
                (int) $service->estimated_duration_minutes
            );

            $this->ensureSlotIsStillAvailable(
                $validated['preferred_date'],
                $preferredStartTime,
                (int) $validated['service_id'],
                null
            );

            $validated['preferred_start_time'] = $preferredStartTime;
            $validated['preferred_end_time'] = $preferredEndTime;

            session([
                'booking.review_data' => $validated,
            ]);

            return view('public.booking.review', [
                'data' => $validated,
                'service' => $service,
                'dentist' => null,
                'isGuest' => !Auth::check(),
            ]);
        } catch (Throwable $e) {
            return back()
                ->withErrors([
                    'preferred_start_time' => $e->getMessage() ?: 'The selected time slot is no longer available.',
                ])
                ->withInput();
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $data = session('booking.review_data');

        if (!$data || !is_array($data)) {
            return redirect()
                ->route(Auth::check() ? 'booking.create' : 'booking.entry')
                ->withErrors([
                    'booking' => 'Your booking session has expired. Please review your booking again.',
                ]);
        }

        $requestCode = 'REQ-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));

        try {
            $service = Service::query()->findOrFail((int) $data['service_id']);

            $preferredStartTime = $this->normalizeTimeValue($data['preferred_start_time']);
            $preferredEndTime = !empty($data['preferred_end_time'])
                ? $this->normalizeTimeValue($data['preferred_end_time'])
                : $this->computeEndTime(
                    $data['preferred_date'],
                    $preferredStartTime,
                    (int) $service->estimated_duration_minutes
                );

            $this->ensureSlotIsStillAvailable(
                $data['preferred_date'],
                $preferredStartTime,
                (int) $data['service_id'],
                null
            );

            $notesPayload = $this->buildNotesPayload(array_merge($data, [
                'preferred_start_time' => $preferredStartTime,
                'preferred_end_time' => $preferredEndTime,
            ]));

            $appointmentRequest = DB::transaction(function () use (
                $data,
                $requestCode,
                $preferredStartTime,
                $preferredEndTime,
                $notesPayload
            ) {
                $appointmentRequest = AppointmentRequest::create([
                    'request_code' => $requestCode,
                    'patient_id' => $this->resolvePatientId(),
                    'service_id' => (int) $data['service_id'],
                    'preferred_date' => $data['preferred_date'],
                    'preferred_start_time' => $preferredStartTime,
                    'preferred_end_time' => $preferredEndTime,
                    'request_status' => 'pending',
                    'notes' => $notesPayload,

                    // Guest / public capture
                    'guest_first_name' => $data['first_name'] ?? null,
                    'guest_middle_name' => $data['middle_name'] ?? null,
                    'guest_last_name' => $data['last_name'] ?? null,
                    'guest_sex' => $data['sex'] ?? null,
                    'guest_birth_date' => $data['birth_date'] ?? null,
                    'guest_contact_number' => $data['contact_number'] ?? null,
                    'guest_email' => $data['email'] ?? null,
                    'guest_address' => $data['address_line'] ?? ($data['address'] ?? null),
                ]);

                $this->saveAppointmentAnswers(
                    $appointmentRequest->request_id,
                    $data['answers'] ?? []
                );

                return $appointmentRequest;
            });

            $staffUsers = User::query()
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'staff');
                })
                ->get();

            foreach ($staffUsers as $staff) {
                $staff->notify(new NewAppointmentRequestNotification($appointmentRequest));
            }

            session()->forget('booking.review_data');

            return redirect()->route('booking.success', [
                'requestCode' => $requestCode,
            ]);
        } catch (Throwable $e) {
            return redirect()
                ->route(Auth::check() ? 'booking.create' : 'booking.entry')
                ->withErrors([
                    'preferred_start_time' => $e->getMessage() ?: 'Booking could not be saved. Please try again.',
                ])
                ->withInput();
        }
    }

    public function success(string $requestCode): View
    {
        $booking = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        return view('public.booking.success', compact('booking'));
    }

    public function serviceMeta(Service $service): JsonResponse
    {
        return response()->json([
            'service_id' => $service->service_id,
            'service_name' => $service->service_name,
            'description' => $service->description,
            'estimated_duration_minutes' => (int) $service->estimated_duration_minutes,
            'estimated_price' => (float) $service->estimated_price,
        ]);
    }

    public function serviceQuestions(Service $service): JsonResponse
    {
        $questions = ServiceOption::query()
            ->with('values')
            ->where('service_id', $service->service_id)
            ->get();

        return response()->json($questions);
    }

    public function availableDentists(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
        ]);

        return response()->json(
            $this->availabilityService->getAvailableDentists(
                $validated['date'],
                (int) $validated['service_id']
            )
        );
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
            'dentist_id' => ['nullable', 'integer', 'exists:dentists,dentist_id'],
        ]);

        $dentistId = $validated['dentist_id'] ?? null;

        return response()->json([
            'clinic_hours' => $this->availabilityService->getClinicHoursForDate($validated['date']),
            'available_slots' => $this->availabilityService->getAvailableSlots(
                $validated['date'],
                (int) $validated['service_id'],
                $dentistId ? (int) $dentistId : null
            ),
        ]);
    }

    public function calendarAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        return response()->json([
            'dates' => $this->availabilityService->getClinicCalendarAvailabilityForMonth(
                $validated['month']
            ),
        ]);
    }

    protected function normalizePhoneFields(array $validated): array
    {
        $phoneFields = [
            'contact_number',
            'guest_contact_number',
            'emergency_contact_number',
        ];

        foreach ($phoneFields as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = $this->phoneNumberService
                    ->normalizePhilippineMobile($validated[$field]);
            }
        }

        return $validated;
    }

    protected function normalizeTimeValue(string $time): string
    {
        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (Throwable $e) {
            try {
                return Carbon::createFromFormat('H:i:s', $time)->format('H:i:s');
            } catch (Throwable $e2) {
                throw new \InvalidArgumentException('Invalid appointment time format.');
            }
        }
    }

    protected function computeEndTime(string $date, string $startTime, int $durationMinutes): string
    {
        return Carbon::parse($date . ' ' . $startTime)
            ->addMinutes($durationMinutes)
            ->format('H:i:s');
    }

    protected function ensureSlotIsStillAvailable(
        string $date,
        string $startTime,
        int $serviceId,
        ?int $dentistId = null
    ): void {
        $isAvailable = $this->availabilityService->isRequestedSlotAvailable(
            $date,
            $startTime,
            $serviceId,
            $dentistId
        );

        if (!$isAvailable) {
            throw new \RuntimeException('The selected time slot is no longer available.');
        }
    }

    protected function resolvePatientId(): ?int
    {
        if (!Auth::check()) {
            return null;
        }

        return optional(Auth::user()->patient)->patient_id;
    }

    protected function saveAppointmentAnswers(int $requestId, array $answers): void
    {
        if (empty($answers) || !is_array($answers)) {
            return;
        }

        foreach ($answers as $optionId => $answerValue) {
            if (is_array($answerValue)) {
                foreach ($answerValue as $singleValue) {
                    AppointmentRequestAnswer::create([
                        'request_id' => $requestId,
                        'option_id' => (int) $optionId,
                        'selected_value_id' => is_numeric($singleValue) ? (int) $singleValue : null,
                        'answer_text' => is_numeric($singleValue) ? null : (string) $singleValue,
                        'created_at' => now(),
                    ]);
                }
            } else {
                AppointmentRequestAnswer::create([
                    'request_id' => $requestId,
                    'option_id' => (int) $optionId,
                    'selected_value_id' => is_numeric($answerValue) ? (int) $answerValue : null,
                    'answer_text' => is_numeric($answerValue) ? null : (string) $answerValue,
                    'created_at' => now(),
                ]);
            }
        }
    }

    protected function buildNotesPayload(array $data): string
    {
        $payload = [
            'patient_info' => [
                'first_name' => $data['first_name'] ?? null,
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'sex' => $data['sex'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'civil_status' => $data['civil_status'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'email' => $data['email'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
            ],
            'address' => [
                'region' => $data['region'] ?? null,
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
                'barangay' => $data['barangay'] ?? null,
                'address_line' => $data['address_line'] ?? null,
            ],
            'appointment' => [
                'preferred_date' => $data['preferred_date'] ?? null,
                'preferred_start_time' => $data['preferred_start_time'] ?? null,
                'preferred_end_time' => $data['preferred_end_time'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'preferred_dentist_id' => null,
            ],
            'notes_or_concerns' => $data['notes'] ?? null,
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
