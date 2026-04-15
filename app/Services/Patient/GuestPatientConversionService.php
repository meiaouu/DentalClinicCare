<?php

namespace App\Services\Patient;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Services\Booking\PhoneNumberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestPatientConversionService
{
    public function __construct(
        protected PhoneNumberService $phoneNumberService
    ) {
    }

    /**
     * Convert a guest appointment into a linked patient if needed.
     * If a matching patient already exists, reuse that patient.
     */
    public function convertIfNeeded(Appointment $appointment): ?Patient
    {
        $appointment->loadMissing(['request', 'patient']);

        // Already linked to a patient, nothing to do.
        if (!empty($appointment->patient_id)) {
            return $appointment->patient;
        }

        $request = $appointment->request;

        if (!$request) {
            return null;
        }

        return DB::transaction(function () use ($appointment, $request) {
            $payload = $this->buildGuestPayload($request);

            if (!$this->hasEnoughIdentityData($payload)) {
                return null;
            }

            $patient = $this->findExistingPatient($payload);

            if (!$patient) {
                $patient = $this->createPatientFromGuestPayload($payload);
            }

            $this->linkAppointmentAndRequest($appointment, $request, $patient);

            return $patient;
        });
    }

    /**
     * Build a normalized patient payload from appointment request guest fields
     * and the JSON notes payload saved by booking flow.
     */
    protected function buildGuestPayload(AppointmentRequest $request): array
    {
        $notesPayload = [];
        $rawNotes = $request->notes;

        if (is_string($rawNotes) && $rawNotes !== '') {
            $decoded = json_decode($rawNotes, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $notesPayload = $decoded;
            }
        }

        $patientInfo = is_array($notesPayload['patient_info'] ?? null)
            ? $notesPayload['patient_info']
            : [];

        $addressInfo = is_array($notesPayload['address'] ?? null)
            ? $notesPayload['address']
            : [];

        $contactNumber = $request->guest_contact_number ?: ($patientInfo['contact_number'] ?? null);
        $email = $request->guest_email ?: ($patientInfo['email'] ?? null);

        try {
            if (!empty($contactNumber)) {
                $contactNumber = $this->phoneNumberService->normalizePhilippineMobile((string) $contactNumber);
            }
        } catch (\Throwable) {
            // Keep original value if normalization fails, do not crash conversion.
            $contactNumber = $request->guest_contact_number ?: null;
        }

        try {
            if (!empty($patientInfo['emergency_contact_number'])) {
                $patientInfo['emergency_contact_number'] = $this->phoneNumberService
                    ->normalizePhilippineMobile((string) $patientInfo['emergency_contact_number']);
            }
        } catch (\Throwable) {
            // Keep original value if normalization fails.
        }

        $address = trim(collect([
            $addressInfo['address_line'] ?? null,
            $addressInfo['barangay'] ?? null,
            $addressInfo['city'] ?? null,
            $addressInfo['province'] ?? null,
            $addressInfo['region'] ?? null,
        ])->filter()->implode(', '));

        return [
            'first_name' => $this->cleanString($request->guest_first_name ?: ($patientInfo['first_name'] ?? null)),
            'middle_name' => $this->cleanString($request->guest_middle_name ?: ($patientInfo['middle_name'] ?? null)),
            'last_name' => $this->cleanString($request->guest_last_name ?: ($patientInfo['last_name'] ?? null)),
            'sex' => $this->cleanString($patientInfo['sex'] ?? null),
            'birth_date' => $patientInfo['birth_date'] ?? null,
            'civil_status' => $this->cleanString($patientInfo['civil_status'] ?? null),
            'occupation' => $this->cleanString($patientInfo['occupation'] ?? null),
            'contact_number' => $this->cleanString($contactNumber),
            'email' => !empty($email) ? mb_strtolower(trim((string) $email)) : null,
            'emergency_contact_name' => $this->cleanString($patientInfo['emergency_contact_name'] ?? null),
            'emergency_contact_number' => $this->cleanString($patientInfo['emergency_contact_number'] ?? null),
            'address' => $address !== '' ? $address : null,
            'notes' => $this->cleanString($notesPayload['notes_or_concerns'] ?? null),
        ];
    }

    /**
     * Require enough identifying info to prevent unsafe patient creation.
     */
    protected function hasEnoughIdentityData(array $payload): bool
    {
        return !empty($payload['contact_number'])
            || !empty($payload['email'])
            || (
                !empty($payload['first_name'])
                && !empty($payload['last_name'])
                && !empty($payload['birth_date'])
            );
    }

    /**
     * Duplicate detection strategy:
     * 1) normalized contact number
     * 2) email + birth_date
     * 3) first_name + last_name + middle_name(optional) + birth_date
     */
    protected function findExistingPatient(array $payload): ?Patient
    {
        if (!empty($payload['contact_number'])) {
            $patient = Patient::query()
                ->where('contact_number', $payload['contact_number'])
                ->first();

            if ($patient) {
                return $patient;
            }
        }

        if (!empty($payload['email']) && !empty($payload['birth_date'])) {
            $patient = Patient::query()
                ->whereRaw('LOWER(email) = ?', [$payload['email']])
                ->whereDate('birth_date', $payload['birth_date'])
                ->first();

            if ($patient) {
                return $patient;
            }
        }

        if (!empty($payload['first_name']) && !empty($payload['last_name']) && !empty($payload['birth_date'])) {
            $query = Patient::query()
                ->whereRaw('LOWER(first_name) = ?', [mb_strtolower($payload['first_name'])])
                ->whereRaw('LOWER(last_name) = ?', [mb_strtolower($payload['last_name'])])
                ->whereDate('birth_date', $payload['birth_date']);

            if (!empty($payload['middle_name'])) {
                $query->whereRaw('LOWER(COALESCE(middle_name, "")) = ?', [mb_strtolower($payload['middle_name'])]);
            }

            $patient = $query->first();

            if ($patient) {
                return $patient;
            }
        }

        return null;
    }

    /**
     * Create a new patient record from guest payload.
     */
    protected function createPatientFromGuestPayload(array $payload): Patient
    {
        return Patient::query()->create([
            'user_id' => null,
            'patient_code' => $this->generatePatientCode(),
            'first_name' => $payload['first_name'],
            'middle_name' => $payload['middle_name'],
            'last_name' => $payload['last_name'],
            'sex' => $payload['sex'],
            'birth_date' => $payload['birth_date'],
            'civil_status' => $payload['civil_status'],
            'address' => $payload['address'],
            'occupation' => $payload['occupation'],
            'contact_number' => $payload['contact_number'],
            'email' => $payload['email'],
            'emergency_contact_name' => $payload['emergency_contact_name'],
            'emergency_contact_number' => $payload['emergency_contact_number'],
            'notes' => $payload['notes'],
            'profile_status' => 'active',
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Link both the appointment and its source request to the patient.
     */
    protected function linkAppointmentAndRequest(
        Appointment $appointment,
        AppointmentRequest $request,
        Patient $patient
    ): void {
        if ((int) ($appointment->patient_id ?? 0) !== (int) $patient->patient_id) {
            $appointment->update([
                'patient_id' => $patient->patient_id,
            ]);
        }

        if ((int) ($request->patient_id ?? 0) !== (int) $patient->patient_id) {
            $request->update([
                'patient_id' => $patient->patient_id,
            ]);
        }
    }

    /**
     * Create a unique patient code.
     */
    protected function generatePatientCode(): string
    {
        do {
            $code = 'PAT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (
            Patient::query()->where('patient_code', $code)->exists()
        );

        return $code;
    }

    protected function cleanString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = trim($value);

        return $cleaned === '' ? null : $cleaned;
    }
}
