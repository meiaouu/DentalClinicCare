<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Services\Messaging\MessageThreadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicMessageController extends Controller
{
    public function patientForm(): View|RedirectResponse
    {
        $patient = Auth::user()?->patient;

        if (!$patient) {
            return redirect()->route('login')->withErrors([
                'auth' => 'Only logged-in patients can send messages here.',
            ]);
        }

        return view('public.messages.patient');
    }

    public function patientSend(Request $request, MessageThreadService $messageService): RedirectResponse
    {
        $patient = Auth::user()?->patient;

        if (!$patient) {
            return redirect()->route('login')->withErrors([
                'auth' => 'Only logged-in patients can send messages here.',
            ]);
        }

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $messageService->sendPatientMessage(
            patient: $patient,
            messageBody: $validated['message_body'],
            subject: $validated['subject'] ?? null
        );

        return back()->with('success', 'Message sent to the clinic.');
    }

    public function guestForm(string $requestCode): View
    {
        $appointmentRequest = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        return view('public.messages.guest', compact('appointmentRequest'));
    }

    public function guestSend(
        Request $request,
        string $requestCode,
        MessageThreadService $messageService
    ): RedirectResponse {
        $appointmentRequest = AppointmentRequest::query()
            ->where('request_code', $requestCode)
            ->firstOrFail();

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message_body' => ['required', 'string', 'max:5000'],
        ]);

        $messageService->sendGuestRequestMessage(
            appointmentRequest: $appointmentRequest,
            messageBody: $validated['message_body'],
            subject: $validated['subject'] ?? null
        );

        return back()->with('success', 'Message sent to the clinic.');
    }
}
