<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the staff login page.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate an authorised hospital staff member.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Normalise email to avoid case-related login inconsistencies.
        $credentials['email'] = Str::lower(trim($credentials['email']));

        // Create a unique rate-limit key using email and IP address.
        $throttleKey = $credentials['email'].'|'.$request->ip();

        // Allow a maximum of 5 failed attempts within 60 seconds.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Verify the staff member's credentials.
        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Clear failed attempts after successful authentication.
        RateLimiter::clear($throttleKey);

        // Regenerate session ID to prevent session fixation.
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Welcome back, '.Auth::user()->name.'!');
    }

    /**
     * Log out the authenticated staff member.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate the session and regenerate its CSRF token.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
