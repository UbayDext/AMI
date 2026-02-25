<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    /**
     * If an authenticated non-admin user has been deactivated,
     * log them out and redirect to the "time is over" page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check on login/deactivated pages to avoid redirect loops
        if ($request->routeIs('login', 'deactivated')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->hasRole('admin')) {
                // Check active status
                if (! $user->is_active) {
                    Auth::logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('deactivated');
                }

                // Check time window
                if ($user->login_start_time && $user->login_end_time) {
                    $now = now()->timezone('Asia/Jakarta')->format('H:i');
                    $start = $user->login_start_time->format('H:i');
                    $end = $user->login_end_time->format('H:i');

                    $isAllowed = false;
                    if ($start <= $end) {
                        $isAllowed = ($now >= $start && $now <= $end);
                    } else {
                        $isAllowed = ($now >= $start || $now <= $end);
                    }

                    if (! $isAllowed) {
                        Auth::logout();

                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        // Redirect back to login with message (we can't use deactivate view because it's a specific message)
                        // Or we can just redirect to deactivated page. Let's redirect to login page with an error.
                        return redirect()->route('login')
                            ->with('error', 'Sesi Anda telah berakhir. Anda hanya dapat login antara pukul ' . $user->login_start_time->format('H:i') . ' - ' . $user->login_end_time->format('H:i') . ' WIB.');
                    }
                }
            }
        }

        return $next($request);
    }
}
