<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleName = strtolower(trim($user?->role?->role_name ?? ''));

        $allowedRoles = array_map(function ($role) {
            return strtolower(trim($role));
        }, $roles);

        if (!in_array($roleName, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
