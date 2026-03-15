<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionStarted
{
    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->getSession();
        $sessionId = $session->getId();
        
        // CRITICAL: Always restore from database if user_id is missing
        if (!$session->has('user_id') && $sessionId) {
            try {
                // Get the session record from database
                $dbSession = \DB::table(config('session.table'))
                    ->where('id', $sessionId)
                    ->first();
                
                // If session exists in database and has user_id
                if ($dbSession && $dbSession->user_id) {
                    // Get user details
                    $user = \DB::table('users')
                        ->where('id', $dbSession->user_id)
                        ->first(['id', 'full_name', 'username', 'role']);
                    
                    if ($user) {
                        // Restore ALL session data from database
                        $session->put('user_id', $dbSession->user_id);
                        $session->put('user_name', $user->full_name ?? $user->username);
                        $session->put('user_role', strtolower($user->role));
                        $session->put('last_activity_timestamp', $dbSession->last_activity ?? time());
                        
                        // CRITICAL: Save the session immediately
                        $session->save();
                        
                        \Log::info('EnsureSessionStarted - Session restored from DB for user: ' . $dbSession->user_id);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('EnsureSessionStarted error: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}





