<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Allow requests from allowed origins
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:8000',
            'http://localhost:3000',
            'http://localhost',
            env('APP_URL', 'http://localhost'),
        ];

        $origin = $request->header('Origin');
        if (in_array($origin, $allowedOrigins)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-User-Id, X-User-Role');
            $response->header('Access-Control-Max-Age', '3600');
        }

        // Handle preflight requests
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $origin ?? '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-User-Id, X-User-Role');
        }

        return $response;
    }
}
