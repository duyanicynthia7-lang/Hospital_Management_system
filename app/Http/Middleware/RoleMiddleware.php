<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users whose `role` column matches one of the allowed roles.
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
