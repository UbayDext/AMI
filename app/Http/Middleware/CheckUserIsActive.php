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

            if (! $user->hasRole('admin') && ! $user->is_active) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('deactivated');
            }
        }

        return $next($request);
    }
}
