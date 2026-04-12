<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Booking\PhoneNumberService;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class RegisterController extends Controller
{
    public function __construct(
        protected PhoneNumberService $phoneNumberService
    ) {
    }

    public function showRegisterForm(Request $request): View
    {
        return view('auth.register', [
            'redirect' => $request->query('redirect', route('booking.create')),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sex' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'contact_number' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'redirect' => ['nullable', 'string'],
        ]);

        $normalizedContact = $this->phoneNumberService
            ->normalizePhilippineMobile($validated['contact_number']);

        $patientRole = Role::query()
            ->where('role_name', 'patient')
            ->first();

        if (!$patientRole) {
            return back()->withErrors([
                'email' => 'Patient role is missing. Please seed roles first.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            $user = DB::transaction(function () use ($validated, $normalizedContact, $patientRole) {
                $user = User::create([
                    'role_id' => $patientRole->role_id,
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'sex' => $validated['sex'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'contact_number' => $normalizedContact,
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'password' => Hash::make($validated['password']),
                    'is_active' => 1,
                ]);

                Patient::create([
                    'user_id' => $user->user_id,
                    'patient_code' => 'PAT-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'sex' => $validated['sex'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'civil_status' => null,
                    'address' => null,
                    'occupation' => null,
                    'contact_number' => $normalizedContact,
                    'email' => $validated['email'],
                    'emergency_contact_name' => null,
                    'emergency_contact_number' => null,
                    'notes' => null,
                    'profile_status' => 'active',
                    'created_by' => null,
                ]);

                return $user;
            });
        } catch (Throwable $e) {
            return back()->withErrors([
                'email' => 'Registration failed. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        $redirect = $validated['redirect'] ?? route('booking.create');

        return redirect()->to($redirect);
    }
}
