<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogAllSessions
{
    public function handle(Request $request, Closure $next): Response
    {
        // Log PHP-level session info
        \Log::info('Session at request start', [
            'php_session_id' => session_id(),
            'php_session_status' => session_status(),
            'cookie_from_request' => $request->cookie(config('session.cookie')),
            'laravel_session_id' => session()->getId(),
            'php_cookies' => $_COOKIE,
        ]);
        
        return $next($request);
    }
}
