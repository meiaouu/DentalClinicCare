<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingReviewRequest;
use App\Models\AppointmentRequest;
use App\Models\AppointmentRequestAnswer;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Services\Booking\BookingAvailabilityService;
use App\Services\Booking\PhoneNumberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

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

    public function guestForm(): View
    {
        $services = Service::query()
            ->where('is_active', 1)
            ->orderBy('service_name')
            ->get();

        $dentists = Dentist::query()
            ->where('is_active', 1)
            ->get();

        return view('public.booking.form', [
            'services' => $services,
            'dentists' => $dentists,
            'isGuest' => true,
            'patient' => null,
        ]);
    }

    public function create(): View
    {
        $services = Service::query()
            ->where('is_active', 1)
            ->orderBy('service_name')
            ->get();

        $dentists = Dentist::query()
            ->where('is_active', 1)
            ->get();

        $patient = optional(Auth::user())->patient;

        return view('public.booking.form', [
            'services' => $services,
            'dentists' => $dentists,
            'isGuest' => false,
            'patient' => $patient,
        ]);
    }

    public function review(BookingReviewRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();

        try {
            if (!empty($validated['contact_number'])) {
                $validated['contact_number'] = $this->phoneNumberService
                    ->normalizePhilippineMobile($validated['contact_number']);
            }

            if (!empty($validated['guest_contact_number'])) {
                $validated['guest_contact_number'] = $this->phoneNumberService
                    ->normalizePhilippineMobile($validated['guest_contact_number']);
            }

            if (!empty($validated['emergency_contact_number'])) {
                $validated['emergency_contact_number'] = $this->phoneNumberService
                    ->normalizePhilippineMobile($validated['emergency_contact_number']);
            }
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withErrors(['contact_number' => $e->getMessage()])
                ->withInput();
        }

        $service = Service::query()->findOrFail($validated['service_id']);
        $dentist = null;

        if (!empty($validated['preferred_dentist_id'])) {
            $dentist = Dentist::query()->find($validated['preferred_dentist_id']);
        }

        $selectedSlotStillAvailable = $this->availabilityService->isRequestedSlotAvailable(
            $validated['preferred_date'],
            $validated['preferred_start_time'],
            (int) $validated['service_id'],
            !empty($validated['preferred_dentist_id']) ? (int) $validated['preferred_dentist_id'] : null
        );

        if (!$selectedSlotStillAvailable) {
            return back()
                ->withErrors([
                    'preferred_start_time' => 'The selected time slot is no longer available.',
                ])
                ->withInput();
        }

        session([
            'booking.review_data' => $validated,
        ]);

        return view('public.booking.review', [
            'data' => $validated,
            'service' => $service,
            'dentist' => $dentist,
            'isGuest' => !Auth::check(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = session('booking.review_data');

        if (!$data) {
            return redirect()
                ->route('booking.entry')
                ->with('error', 'Booking session expired. Please fill out the form again.');
        }

        $slotStillAvailable = $this->availabilityService->isRequestedSlotAvailable(
            $data['preferred_date'],
            $data['preferred_start_time'],
            (int) $data['service_id'],
            !empty($data['preferred_dentist_id']) ? (int) $data['preferred_dentist_id'] : null
        );

        if (!$slotStillAvailable) {
            return redirect()
                ->route(Auth::check() ? 'booking.create' : 'booking.guest.form')
                ->withErrors([
                    'preferred_start_time' => 'The selected time slot is no longer available.',
                ]);
        }

        $requestCode = 'REQ-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));

        try {
            DB::transaction(function () use ($data, $requestCode) {
                $patientId = null;

                if (Auth::check() && Auth::user()->patient) {
                    $patientId = Auth::user()->patient->patient_id;
                }

                $appointmentRequest = AppointmentRequest::create([
                    'request_code' => $requestCode,
                    'patient_id' => $patientId,
                    'guest_first_name' => $data['guest_first_name'] ?? null,
                    'guest_middle_name' => $data['guest_middle_name'] ?? null,
                    'guest_last_name' => $data['guest_last_name'] ?? null,
                    'guest_contact_number' => $data['guest_contact_number'] ?? null,
                    'guest_email' => $data['guest_email'] ?? null,
                    'preferred_dentist_id' => $data['preferred_dentist_id'] ?? null,
                    'service_id' => $data['service_id'],
                    'preferred_date' => $data['preferred_date'],
                    'preferred_start_time' => $data['preferred_start_time'],
                    'notes' => $this->buildNotesPayload($data),
                    'request_status' => 'pending',
                ]);

                if (!empty($data['answers']) && is_array($data['answers'])) {
                    foreach ($data['answers'] as $optionId => $answerValue) {
                        if (is_array($answerValue)) {
                            foreach ($answerValue as $singleValue) {
                                AppointmentRequestAnswer::create([
                                    'request_id' => $appointmentRequest->request_id,
                                    'option_id' => (int) $optionId,
                                    'selected_value_id' => is_numeric($singleValue) ? (int) $singleValue : null,
                                    'answer_text' => is_numeric($singleValue) ? null : (string) $singleValue,
                                    'created_at' => now(),
                                ]);
                            }
                        } else {
                            AppointmentRequestAnswer::create([
                                'request_id' => $appointmentRequest->request_id,
                                'option_id' => (int) $optionId,
                                'selected_value_id' => is_numeric($answerValue) ? (int) $answerValue : null,
                                'answer_text' => is_numeric($answerValue) ? null : (string) $answerValue,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route(Auth::check() ? 'booking.create' : 'booking.guest.form')
                ->with('error', 'Booking could not be saved. Please try again.');
        }

        session()->forget('booking.review_data');

        return redirect()->route('booking.success', ['requestCode' => $requestCode]);
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

        return response()->json(
            $this->availabilityService->getAvailableSlots(
                $validated['date'],
                (int) $validated['service_id'],
                !empty($validated['dentist_id']) ? (int) $validated['dentist_id'] : null
            )
        );
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
            'notes_or_concerns' => $data['notes'] ?? null,
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
