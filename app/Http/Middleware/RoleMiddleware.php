<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtolower(trim($user?->role?->role_name ?? ''));

        $allowedRoles = array_map(
            fn ($role) => strtolower(trim($role)),
            $roles
        );

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
