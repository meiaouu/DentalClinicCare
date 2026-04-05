<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();

        return match (optional($user->role)->role_name) {
            RoleName::ADMIN->value => redirect()->route('admin.dashboard'),
            RoleName::STAFF->value => redirect()->route('staff.dashboard'),
            RoleName::DENTIST->value => redirect()->route('dentist.dashboard'),
            RoleName::PATIENT->value => redirect()->route('patient.dashboard'),
            default => redirect()->route('home'),
        };
    }

    public function admin(): View
    {
        return view('admin.dashboard');
    }

    public function staff(): View
    {
        return view('staff.dashboard');
    }

    public function dentist(): View
    {
        return view('dentist.dashboard');
    }

    public function patient(): View
    {
        return view('patient.dashboard');
    }
}
