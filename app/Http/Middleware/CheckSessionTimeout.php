<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // Session timeout disabled - users stay logged in indefinitely
        // To enable timeout checking, uncomment the code below and set SESSION_TIMEOUT config
        
        // Update last activity time using Laravel session
        $currentTime = now()->timestamp;
        session()->put('last_activity_timestamp', $currentTime);
        
        return $next($request);
    }
}

