<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\Patient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LogActivityMiddleware
 *
 * Automatically logs route-level events without polluting controllers.
 * Specific business events (appointment confirmed, visit added) are logged
 * directly in their controllers for more context.
 */
class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log successful GET/POST responses from authenticated users
        if (!$request->user() || $response->getStatusCode() >= 400) {
            return $response;
        }

        // Auto-log patient profile views
        if ($request->isMethod('GET') && $request->route('patient')) {
            $patient = $request->route('patient');
            if ($patient instanceof Patient) {
                ActivityLog::log(
                    action:      'patient.viewed',
                    description: "Viewed patient record: {$patient->full_name}",
                    severity:    'info',
                    subject:     $patient,
                );
            }
        }

        return $response;
    }
}