<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('user_id');
        $sessionId = session()->getId();
        $cookieName = config('session.cookie');
        $requestCookieHeader = $request->header('Cookie');
        
        // Log session data for debugging
        \Log::debug('CheckAuth Middleware - Detailed Diagnostic', [
            'path' => $request->path(),
            'method' => $request->method(),
            'user_id_found' => $userId,
            'session_id' => $sessionId,
            'cookie_name_expected' => $cookieName,
            'cookie_header_sent' => $requestCookieHeader ?: 'NO COOKIE HEADER IN REQUEST',
            'has_cookie_from_hasCookie' => $request->hasCookie($cookieName),
            'all_cookies_in_request' => array_keys($request->cookies->all()),
            'session_has_user_id_key' => session()->has('user_id'),
            'full_session_array' => session()->all(),
            'session_driver' => config('session.driver'),
        ]);
        
        if (!$userId) {
            \Log::warning('CheckAuth - Unauthorized Access Attempt', [
                'path' => $request->path(),
                'reason' => 'No user_id in session',
                'session_id' => $sessionId,
                'has_cookie' => $request->hasCookie($cookieName),
                'session_data' => session()->all(),
            ]);
            
            // For API requests, return JSON error
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized - Please log in',
                    'debug' => [
                        'user_id' => $userId,
                        'session_has_user_id' => session()->has('user_id'),
                        'session_id' => $sessionId,
                    ]
                ], 401);
            }
            
            // For web requests, redirect to login
            return redirect()->route('login');
        }

        return $next($request);
    }
}
