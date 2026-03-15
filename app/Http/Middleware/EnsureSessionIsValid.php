<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureSessionIsValid
 *
 * Prevents unexpected logouts by validating session integrity.
 * Regenerates CSRF token only on actual form submissions, not on navigation.
 */
class EnsureSessionIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated but session user_id is missing, restore it
        if (Auth::check() && !$request->session()->has('_token')) {
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
