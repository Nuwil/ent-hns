<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Try to restore session data from database if missing
        // This is optional - just makes sure user info is available
        $sessionId = session()->getId();
        
        if (!session('user_id') && $sessionId) {
            try {
                $dbSession = \DB::table(config('session.table'))
                    ->where('id', $sessionId)
                    ->first(['user_id', 'last_activity']);
                
                if ($dbSession && $dbSession->user_id) {
                    $user = \DB::table('users')
                        ->where('id', $dbSession->user_id)
                        ->first(['id', 'full_name', 'username', 'role']);
                    
                    if ($user) {
                        session()->put('user_id', $dbSession->user_id);
                        session()->put('user_name', $user->full_name ?? $user->username);
                        session()->put('user_role', strtolower($user->role));
                        session()->put('last_activity_timestamp', $dbSession->last_activity ?? time());
                        session()->save();
                    }
                }
            } catch (\Exception $e) {
                // Silent fail - session data not critical for access
            }
        }

        return $next($request);
    }
}


