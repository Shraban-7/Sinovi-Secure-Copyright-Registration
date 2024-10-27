<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\SendTwoFactorCode;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        try {
            // Generate and send 2FA code
            $user = $request->user();
            $user->generateTwoFactorCode();
            $user->notify(new SendTwoFactorCode());

            // Redirect to verification page instead of dashboard
            return redirect()->route('verify.index');
        } catch (\Exception $e) {
            // Log the error
            // \Log::error('2FA Generation Failed: ' . $e->getMessage());

            // Logout user if 2FA setup fails
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Failed to setup two-factor authentication.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Reset 2FA code if it exists
        if ($user && $user->two_factor_code) {
            $user->resetTwoFactorCode();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
