<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionLifetime = config('session.lifetime', 3600); // 1 hour default

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user has been idle
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > ($sessionLifetime * 60))) {
            session_unset();
            session_destroy();
            return response()->json(['error' => 'Session expired'], 401);
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();

        return $next($request);
    }
}
