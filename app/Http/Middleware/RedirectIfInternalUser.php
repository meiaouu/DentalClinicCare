<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInternalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = strtolower(trim($user?->role?->role_name ?? ''));

            if ($role === 'staff' && app('router')->has('staff.dashboard')) {
                return redirect()->route('staff.dashboard');
            }

            if ($role === 'admin' && app('router')->has('admin.dashboard')) {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'dentist' && app('router')->has('dentist.dashboard')) {
                return redirect()->route('dentist.dashboard');
            }
        }

        return $next($request);
    }
}
