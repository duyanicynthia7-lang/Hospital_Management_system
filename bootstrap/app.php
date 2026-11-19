
<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))

    // Register application routes
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Configure application middleware
    ->withMiddleware(function (Middleware $middleware): void {

        // Trust Render's reverse proxy to detect HTTPS correctly
        $middleware->trustProxies(at: '*');

        // Register custom role-based middleware
        // Usage: ->middleware('role:admin,doctor')
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Redirect unauthenticated users to the login page
        $middleware->redirectGuestsTo(
            fn () => route('login')
        );

        // Redirect authenticated users away from guest pages
        $middleware->redirectUsersTo(
            fn () => route('dashboard')
        );
    })

    // Configure exception handling
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
