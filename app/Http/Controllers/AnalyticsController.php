<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // ── Page load — pass doctors list + role context ─────────────
    public function index()
    {
        $user    = Auth::user();
        $isAdmin = $user->role === 'admin';
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get(['id', 'full_name']);

        $role           = $user->role;
        $clinicDataUrl  = route("{$role}.analytics.clinic");
        $doctorDataUrl  = route("{$role}.analytics.doctor");

        return view('analytics.index', compact('doctors', 'isAdmin', 'clinicDataUrl', 'doctorDataUrl'));
    }

    // ── AJAX: Doctor Performance Data ────────────────────────────
    public function doctorData(Request $request)
    {
        [$start, $end] = $this->parseDateRange($request);
        $user = Auth::user();

        // Doctor sees only themselves — admin can select any/all
        if ($user->role === 'doctor') {
            $doctorIds = [$user->id];
        } else {
            $doctorIds = $request->filled('doctor_ids')
                ? explode(',', $request->doctor_ids)
                : User::where('role', 'doctor')->pluck('id')->toArray();
        }

        $doctors = User::whereIn('id', $doctorIds)->get(['id', 'full_name']);
        $result  = [];

        foreach ($doctors as $doc) {
            $visits = Visit::where('doctor_id', $doc->id)
                ->whereBetween('visited_at', [$start, $end]);

            $totalVisits     = (clone $visits)->count();
            $finalizedVisits = (clone $visits)->where('status', 'finalized')->count();
            $totalPatients   = (clone $visits)->distinct('patient_id')->count('patient_id');

            // Avg consultations per day
            $days    = max($start->diffInDays($end), 1);
            $avgPerDay = round($totalVisits / $days, 1);

            // Top ENT classification
            $topEnt = (clone $visits)
                ->whereNotNull('ent_classification')
                ->selectRaw('ent_classification, COUNT(*) as cnt')
                ->groupBy('ent_classification')
                ->orderByDesc('cnt')
                ->value('ent_classification') ?? '—';

            // Completion rate (finalized / total)
            $completionRate = $totalVisits > 0
                ? round(($finalizedVisits / $totalVisits) * 100) : 0;

            // Appointment acceptance rate
            $totalAppts    = Appointment::where('doctor_id', $doc->id)
                ->whereBetween('scheduled_at', [$start, $end])->count();
            $acceptedAppts = Appointment::where('doctor_id', $doc->id)
                ->whereIn('status', ['accepted', 'completed'])
                ->whereBetween('scheduled_at', [$start, $end])->count();
            $acceptanceRate = $totalAppts > 0
                ? round(($acceptedAppts / $totalAppts) * 100) : 0;

            // Visits trend (grouped by day/week/month depending on range)
            $trend = $this->buildTrend($doc->id, $start, $end);

            // ENT classification breakdown
            $entBreakdown = (clone $visits)
                ->whereNotNull('ent_classification')
                ->selectRaw('ent_classification, COUNT(*) as cnt')
                ->groupBy('ent_classification')
                ->orderByDesc('cnt')
                ->pluck('cnt', 'ent_classification');

            $result[] = [
                'id'             => $doc->id,
                'name'           => $doc->full_name,
                'totalVisits'    => $totalVisits,
                'finalizedVisits'=> $finalizedVisits,
                'totalPatients'  => $totalPatients,
                'avgPerDay'      => $avgPerDay,
                'topEnt'         => $topEnt,
                'completionRate' => $completionRate,
                'acceptanceRate' => $acceptanceRate,
                'totalAppts'     => $totalAppts,
                'trend'          => $trend,
                'entBreakdown'   => $entBreakdown,
            ];
        }

        return response()->json([
            'doctors'   => $result,
            'dateRange' => ['start' => $start->format('M j, Y'), 'end' => $end->format('M j, Y')],
        ]);
    }

    // ── AJAX: Clinic Performance Data ────────────────────────────
    public function clinicData(Request $request)
    {
        [$start, $end] = $this->parseDateRange($request);

        $totalPatients  = Patient::whereBetween('created_at', [$start, $end])->count();
        $totalVisits    = Visit::whereBetween('visited_at', [$start, $end])->count();
        $totalAppts     = Appointment::whereBetween('scheduled_at', [$start, $end])->count();
        $completedVisits = Visit::where('status', 'finalized')->whereBetween('visited_at', [$start, $end])->count();
        $pendingVisits  = Visit::where('status', 'pending')->whereBetween('visited_at', [$start, $end])->count();
        $inProgressVisits = Visit::where('status', 'in_progress')->whereBetween('visited_at', [$start, $end])->count();

        // Appointment statuses
        $apptStats = Appointment::whereBetween('scheduled_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        // Top chief complaints
        $topComplaints = Visit::whereBetween('visited_at', [$start, $end])
            ->whereNotNull('chief_complaint')
            ->where('chief_complaint', '!=', '')
            ->selectRaw('chief_complaint, COUNT(*) as cnt')
            ->groupBy('chief_complaint')
            ->orderByDesc('cnt')
            ->take(8)
            ->pluck('cnt', 'chief_complaint');

        // Top ENT classifications
        $topEnt = Visit::whereBetween('visited_at', [$start, $end])
            ->whereNotNull('ent_classification')
            ->selectRaw('ent_classification, COUNT(*) as cnt')
            ->groupBy('ent_classification')
            ->orderByDesc('cnt')
            ->pluck('cnt', 'ent_classification');

        // Doctor workload
        $workload = Visit::whereBetween('visited_at', [$start, $end])
            ->join('users', 'visits.doctor_id', '=', 'users.id')
            ->selectRaw('users.full_name as doctor, COUNT(*) as cnt')
            ->groupBy('users.full_name')
            ->orderByDesc('cnt')
            ->pluck('cnt', 'doctor');

        // Visit trend over time (grouped by day/week/month for display charts)
        $visitTrend = $this->buildClinicTrend($start, $end);

        // New patients trend
        $patientTrend = $this->buildPatientTrend($start, $end);

        // Forecast trend — ALWAYS daily, capped at last 60 days so the
        // algorithm always gets the same granularity regardless of the
        // selected range (week/month/year views were sending weekly/monthly
        // buckets which broke the OLS slope and produced near-zero forecasts).
        $forecastStart = now()->subDays(59)->startOfDay();
        $forecastTrend = $this->buildDailyTrend($forecastStart, now()->endOfDay());

        return response()->json([
            'totalPatients'    => $totalPatients,
            'totalVisits'      => $totalVisits,
            'totalAppts'       => $totalAppts,
            'completedVisits'  => $completedVisits,
            'pendingVisits'    => $pendingVisits,
            'inProgressVisits' => $inProgressVisits,
            'apptStats'        => $apptStats,
            'topComplaints'    => $topComplaints,
            'topEnt'           => $topEnt,
            'workload'         => $workload,
            'visitTrend'       => $visitTrend,
            'patientTrend'     => $patientTrend,
            'forecastTrend'    => $forecastTrend,
            'dateRange'        => ['start' => $start->format('M j, Y'), 'end' => $end->format('M j, Y')],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function parseDateRange(Request $request): array
    {
        $range = $request->get('range', 'month');

        return match ($range) {
            'today'  => [now()->startOfDay(), now()->endOfDay()],
            'week'   => [now()->startOfWeek(), now()->endOfWeek()],
            'year'   => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                Carbon::parse($request->get('start', now()->subMonth()))->startOfDay(),
                Carbon::parse($request->get('end', now()))->endOfDay(),
            ],
            default  => [now()->startOfMonth(), now()->endOfMonth()], // month
        };
    }

    private function buildTrend(int $doctorId, Carbon $start, Carbon $end): array
    {
        return $this->buildSeriesTrend(
            Visit::where('doctor_id', $doctorId)->whereBetween('visited_at', [$start, $end]),
            'visited_at',
            $start,
            $end
        );
    }

    private function buildClinicTrend(Carbon $start, Carbon $end): array
    {
        return $this->buildSeriesTrend(
            Visit::whereBetween('visited_at', [$start, $end]),
            'visited_at',
            $start,
            $end
        );
    }

    private function buildPatientTrend(Carbon $start, Carbon $end): array
    {
        return $this->buildSeriesTrend(
            Patient::whereBetween('created_at', [$start, $end]),
            'created_at',
            $start,
            $end
        );
    }

    /**
     * Shared trend builder — groups any query by day/week/month
     * depending on the date range and fills gaps with zeroes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $dateColumn  e.g. 'visited_at' or 'created_at'
     */
    /**
     * Always returns daily visit counts for the given range.
     * Used exclusively by the forecast algorithm so it always
     * receives day-granularity data regardless of the selected UI range.
     */
    private function buildDailyTrend(Carbon $start, Carbon $end): array
    {
        $raw = Visit::whereBetween('visited_at', [$start, $end])
            ->selectRaw("DATE(visited_at) as day, COUNT(*) as cnt")
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day');

        $labels  = [];
        $values  = [];
        $current = $start->copy()->startOfDay();

        while ($current->lte($end)) {
            $labels[] = $current->format('M j');
            $values[] = (int) $raw->get($current->format('Y-m-d'), 0);
            $current->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function buildSeriesTrend($query, string $dateColumn, Carbon $start, Carbon $end): array
    {
        $totalDays = $start->diffInDays($end);

        // Choose grouping granularity
        [$sqlFmt, $phpFmt, $labelFmt, $step] = match (true) {
            $totalDays <= 31  => ['%Y-%m-%d', 'Y-m-d', 'M j',  'day'],
            $totalDays <= 365 => ['%Y-%u',    'Y-W',   'M j',  'week'],
            default           => ['%Y-%m',    'Y-m',   'M Y',  'month'],
        };

        $raw = $query
            ->selectRaw("DATE_FORMAT({$dateColumn}, '{$sqlFmt}') as period, COUNT(*) as cnt")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('cnt', 'period');

        $labels  = [];
        $values  = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $labels[] = $current->format($labelFmt);
            $values[] = $raw->get($current->format($phpFmt), 0);

            match ($step) {
                'day'   => $current->addDay(),
                'week'  => $current->addWeek(),
                'month' => $current->addMonth(),
            };
        }

        return ['labels' => $labels, 'values' => $values];
    }
}