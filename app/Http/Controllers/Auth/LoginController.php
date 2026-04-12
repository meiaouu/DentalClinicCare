<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Booking\PhoneNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        return view('auth.login', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'redirect' => ['nullable', 'string'],
        ]);

        $loginInput = trim((string) $validated['login']);
        $password = (string) $validated['password'];

        $credentials = $this->buildCredentials($loginInput, $password);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            return redirect()->to($this->resolveRedirectTarget($validated['redirect'] ?? null));
        }

        return back()
            ->withErrors([
                'login' => 'Invalid credentials.',
            ])
            ->withInput($request->only('login', 'redirect'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function buildCredentials(string $loginInput, string $password): array
    {
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => $loginInput,
                'password' => $password,
                'is_active' => 1,
            ];
        }

        if (preg_match('/^(\+639|639|09)\d{9}$/', $loginInput)) {
            $phoneService = app(PhoneNumberService::class);
            $normalized = $phoneService->normalizePhilippineMobile($loginInput);

            return [
                'contact_number' => $normalized,
                'password' => $password,
                'is_active' => 1,
            ];
        }

        return [
            'username' => $loginInput,
            'password' => $password,
            'is_active' => 1,
        ];
    }

    protected function resolveRedirectTarget(?string $requestedRedirect = null): string
    {
        $roleRedirect = $this->redirectByRole();

        if (!$requestedRedirect) {
            return $roleRedirect;
        }

        if (!$this->isSafeInternalRedirect($requestedRedirect)) {
            return $roleRedirect;
        }

        $roleName = strtolower(trim(Auth::user()?->role?->role_name ?? ''));

        if (in_array($roleName, ['admin', 'staff', 'dentist'], true)) {
            return $roleRedirect;
        }

        return $requestedRedirect;
    }

    protected function isSafeInternalRedirect(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return str_starts_with($path, '/') && !str_starts_with($path, '//');
    }

    protected function redirectByRole(): string
    {
        $user = Auth::user();
        $roleName = strtolower(trim($user?->role?->role_name ?? ''));

        return match ($roleName) {
            'admin' => route('staff.dashboard'),
            'staff' => route('staff.dashboard'),
            'dentist' => route('home'),
            'patient' => route('booking.create'),
            default => route('home'),
        };
    }
}
