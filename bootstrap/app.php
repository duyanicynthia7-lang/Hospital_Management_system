<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register our custom role middleware as "role" so routes can use: ->middleware('role:admin,doctor')
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Where unauthenticated users are sent (auth middleware)
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Where already-logged-in users are sent when visiting guest pages (guest middleware)
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
