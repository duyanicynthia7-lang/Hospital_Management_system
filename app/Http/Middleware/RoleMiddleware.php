<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users whose `role` column matches one of the allowed roles.
 * Super Administrators can access every role-protected route.
 *
 * Usage in routes:  ->middleware('role:admin')  or  ->middleware('role:admin,doctor')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Administrator passes every role check.
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return $next($request);
        }

        // Support both "role:admin,doctor" and "role:admin|doctor"
        $allowed = [];
        foreach ($roles as $role) {
            foreach (preg_split('/[|,]/', $role) as $r) {
                $r = trim($r);
                if ($r !== '') {
                    $allowed[] = $r;
                }
            }
        }

        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}