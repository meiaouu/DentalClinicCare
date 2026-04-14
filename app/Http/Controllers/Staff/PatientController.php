<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StorePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class PatientController extends Controller
{
    public function index(): View
    {
        $patients = Patient::query()
            ->latest('patient_id')
            ->paginate(15);

        return view('staff.patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('staff.patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $patient = Patient::create([
            'user_id' => null,
            'patient_code' => 'PT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'sex' => $data['sex'],
            'birth_date' => $data['birth_date'],
            'civil_status' => $data['civil_status'] ?? null,
            'address' => $data['address'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'contact_number' => $data['contact_number'],
            'email' => $data['email'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'profile_status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('staff.patients.index')
            ->with('success', 'Patient created successfully. Patient Code: ' . $patient->patient_code);
    }
    public function show(\App\Models\Patient $patient): \Illuminate\View\View
{
    $patient->load([
        'appointments.service',
        'appointments.dentist.user',
        'appointmentRequests.service',
    ]);

    $stats = $patient->appointment_status_summary;

    return view('staff.patients.show', compact('patient', 'stats'));
}
}
