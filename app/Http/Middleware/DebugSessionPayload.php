<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugSessionPayload
{
    /**
     * Log the actual session payload from the database for debugging
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get session ID from cookie
        $cookieName = config('session.cookie');
        $sessionId = $request->cookies->get($cookieName);
        
        if ($sessionId && config('session.driver') === 'database') {
            try {
                $sessionRecord = \DB::table(config('session.table'))
                    ->where('id', $sessionId)
                    ->first();
                
                if ($sessionRecord && $sessionRecord->payload) {
                    // Try to decode the payload
                    $decoded = collect(unserialize($sessionRecord->payload) ?? []);
                    
                    \Log::debug('DebugSessionPayload - Raw Database Payload', [
                        'session_id' => substr($sessionId, 0, 20) . '...',
                        'payload_length' => strlen($sessionRecord->payload),
                        'decoded_keys' => $decoded->keys()->all(),
                        'has_user_id' => $decoded->has('user_id'),
                        'user_id_value' => $decoded->get('user_id'),
                        'path' => $request->path(),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('DebugSessionPayload - Error', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return $next($request);
    }
}
