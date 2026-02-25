<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        $user = Auth::user();

        // Non-admin accounts require admin approval before access is granted.
        if (! $user->hasRole('admin') && ! $user->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda belum diaktifkan. Silakan hubungi admin.');
        }

        // Time window check for non-admin accounts
        if (! $user->hasRole('admin') && $user->login_start_time && $user->login_end_time) {
            $now = now()->timezone('Asia/Jakarta')->format('H:i');
            $start = $user->login_start_time->format('H:i');
            $end = $user->login_end_time->format('H:i');

            $isAllowed = false;
            if ($start <= $end) {
                // e.g., 08:00 to 17:00
                $isAllowed = ($now >= $start && $now <= $end);
            } else {
                // e.g., 22:00 to 06:00 (next day)
                $isAllowed = ($now >= $start || $now <= $end);
            }

            if (! $isAllowed) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $timeMsg = $user->login_start_time->format('H:i') . ' - ' . $user->login_end_time->format('H:i') . ' WIB';
                return redirect()->route('login')
                    ->with('error', 'Anda hanya dapat login antara pukul ' . $timeMsg . '.');
            }
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
