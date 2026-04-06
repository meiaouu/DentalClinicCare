<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\ConfirmBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Services\Appointment\AppointmentReviewService;
use App\Services\Appointment\AppointmentService;
use App\Services\Schedule\SlotGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create()
    {
        return view('public.booking', [
            'isGuest' => auth()->guest(),
            'patient' => auth()->user(),
            'prefillContact' => null,
            'services' => DB::table('services')->get(),
            'dentists' => DB::table('dentists')->where('is_active', 1)->get(),
        ]);
    }

    public function serviceQuestions(int $serviceId)
    {
        $questions = DB::table('service_options')
            ->where('service_id', $serviceId)
            ->where('is_active', 1)
            ->get()
            ->map(function ($q) {
                $q->values = DB::table('service_option_values')
                    ->where('option_id', $q->option_id)
                    ->where('is_active', 1)
                    ->get();
                return $q;
            });

        return response()->json($questions);
    }

    public function availableSlots(Request $request, SlotGeneratorService $slotGeneratorService)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'dentist_id' => ['nullable', 'integer'],
        ]);

        return response()->json(
            $slotGeneratorService->generate(
                $validated['date'],
                (int) $validated['service_id'],
                isset($validated['dentist_id']) ? (int) $validated['dentist_id'] : null
            )
        );
    }

    public function review(
        StoreBookingRequest $request,
        AppointmentService $appointmentService,
        AppointmentReviewService $reviewService
    ) {
        $validated = $request->validated();

        $startTime = strlen($validated['preferred_start_time']) === 5
            ? $validated['preferred_start_time'] . ':00'
            : $validated['preferred_start_time'];

        $validated['preferred_end_time'] = $appointmentService->computeEndTime(
            (int) $validated['service_id'],
            $validated['preferred_date'],
            $startTime
        );

        $appointmentService->validateScheduleRules(
            $validated['preferred_date'],
            $startTime,
            $validated['preferred_end_time'],
            $validated['preferred_dentist_id'] ?? null
        );

        $reviewToken = $reviewService->store($validated);

        return view('public.booking-review', [
            'reviewToken' => $reviewToken,
            'booking' => $validated,
        ]);
    }

    public function confirm(
        ConfirmBookingRequest $request,
        AppointmentReviewService $reviewService,
        AppointmentService $appointmentService
    ) {
        $payload = $reviewService->get($request->review_token);

        if (!$payload) {
            return redirect()->route('booking.create')
                ->withErrors(['review_token' => 'Booking review session expired.']);
        }

        $appointmentService->createPendingRequest($payload);
        $reviewService->forget($request->review_token);

        return redirect()->route('booking.create')
            ->with('success', 'Your appointment request has been submitted and is now pending approval.');
    }
}
