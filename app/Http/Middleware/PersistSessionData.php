<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersistSessionData
{
    public function handle(Request $request, Closure $next): Response
    {
        // Process the request
        $response = $next($request);
        
        // CRITICAL: After request, absolutelyensure session is saved to database
        if (session()->has('user_id')) {
            try {
                $sessionId = session()->getId();
                $userId = session('user_id');
                
                // Get current session data from Laravel
                $userName = session('user_name', 'User');
                $userRole = session('user_role', 'user');
                $lastActivity = session('last_activity_timestamp', time());
                
                // Force save to database with all user data
                \DB::table(config('session.table'))->where('id', $sessionId)->update([
                    'user_id' => $userId,
                    'last_activity' => $lastActivity,
                ]);
                
                // Also save session through Laravel to ensure it's updated
                session()->save();
                
                \Log::debug('PersistSessionData - Saved session for user: ' . $userId);
            } catch (\Exception $e) {
                \Log::error('PersistSessionData error: ' . $e->getMessage());
            }
        }
        
        return $response;
    }
}

