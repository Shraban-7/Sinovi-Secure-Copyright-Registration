<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated and has a 2FA code set
        if (Auth::check() && $user && $user->two_factor_code) {

            // If the two-factor code is expired, log out and redirect to login
            if ($user->two_factor_expires_at < now()) {
                $user->resetTwoFactorCode(); // Reset the 2FA code
                Auth::logout(); // Log out the user

                return redirect()->route('login')
                    ->with('status', 'Your verification code expired. Please re-login.');
            }

            // If the request isn't for the verification page, redirect to it
            if (!$request->is('verify*')) {
                return redirect()->route('verify.index');
            }
        }

        // Allow the request to proceed
        return $next($request);
    }
}
