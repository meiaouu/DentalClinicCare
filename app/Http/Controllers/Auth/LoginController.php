<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        return view('auth.login', [
            'redirect' => $request->query('redirect', route('booking.create')),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'redirect' => ['nullable', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => 1,
        ], $remember)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email', 'redirect'));
        }

        $request->session()->regenerate();

        $redirect = $validated['redirect'] ?? route('booking.create');

        return redirect()->to($redirect);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
