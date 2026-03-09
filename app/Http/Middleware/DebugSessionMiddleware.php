<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookieName = config('session.cookie');
        $cookieFromRequest = $request->cookie($cookieName);
        $sessionId = session()->getId();
        
        \Log::info('=== DEBUG SESSION MIDDLEWARE ===', [
            'path' => $request->path(),
            'method' => $request->method(),
            'cookie_name' => $cookieName,
            'cookie_from_request' => $cookieFromRequest,
            'session_id_from_laravel' => $sessionId,
            'do_they_match' => $cookieFromRequest === $sessionId,
            'session_user_id' => session('user_id'),
            'all_cookies_in_request' => array_keys($request->cookies->all()),
            'request_headers_cookie' => $request->header('Cookie'),
        ]);
        
        // Check if this session exists in database
        if ($sessionId) {
            $dbSession = \DB::table('sessions')->where('id', $sessionId)->first();
            \Log::info('Session DB lookup', [
                'session_id' => $sessionId,
                'found_in_db' => $dbSession ? 'YES' : 'NO',
                'db_user_id' => $dbSession ? $dbSession->user_id : 'N/A',
            ]);
        }
        
        return $next($request);
    }
}
