<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getDashboard(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $stats = [
                'total_patients' => DB::table('patients')->count(),
                'total_appointments' => DB::table('appointments')->count(),
                'completed_appointments' => DB::table('appointments')
                    ->where('status', 'Completed')->count(),
                'pending_appointments' => DB::table('appointments')
                    ->where('status', 'Pending')->count(),
                'total_doctors' => DB::table('users')
                    ->where('role', 'doctor')->where('is_active', true)->count(),
                'total_visits' => DB::table('patient_visits')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMetrics(Request $request): JsonResponse
    {
        try {
            $metricType = $request->get('metric_type');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query = Analytics::query();

            if ($metricType) {
                $query->where('metric_type', $metricType);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('measurement_date', [$startDate, $endDate]);
            }

            $metrics = $query->orderBy('measurement_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
