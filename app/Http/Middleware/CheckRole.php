<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = session('user_role');
        
        if (!$userRole || !in_array($userRole, $roles)) {
            return response()->json([
                'error' => 'Unauthorized - Insufficient permissions'
            ], 403);
        }

        return $next($request);
    }
}
