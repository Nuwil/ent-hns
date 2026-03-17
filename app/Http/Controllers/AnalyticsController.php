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

        // Visit trend over time
        $visitTrend = $this->buildClinicTrend($start, $end);

        // New patients trend
        $patientTrend = $this->buildPatientTrend($start, $end);

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
        $days = $start->diffInDays($end);
        $fmt  = $days <= 31 ? '%Y-%m-%d' : ($days <= 365 ? '%Y-%u' : '%Y-%m');
        $labelFmt = $days <= 31 ? 'M j' : ($days <= 365 ? 'W\eek W' : 'M Y');

        $raw = Visit::where('doctor_id', $doctorId)
            ->whereBetween('visited_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(visited_at, '{$fmt}') as period, COUNT(*) as cnt")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('cnt', 'period');

        // Fill gaps
        $labels = []; $values = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->format($days <= 31 ? 'Y-m-d' : ($days <= 365 ? 'Y-W' : 'Y-m'));
            $labels[] = $current->format($days <= 31 ? 'M j' : ($days <= 365 ? 'M j' : 'M Y'));
            $values[] = $raw->get($key, 0);
            $days <= 31 ? $current->addDay() : ($days <= 365 ? $current->addWeek() : $current->addMonth());
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function buildClinicTrend(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end);
        $fmt  = $days <= 31 ? '%Y-%m-%d' : ($days <= 365 ? '%Y-%u' : '%Y-%m');

        $raw = Visit::whereBetween('visited_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(visited_at, '{$fmt}') as period, COUNT(*) as cnt")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('cnt', 'period');

        $labels = []; $values = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->format($days <= 31 ? 'Y-m-d' : ($days <= 365 ? 'Y-W' : 'Y-m'));
            $labels[] = $current->format($days <= 31 ? 'M j' : ($days <= 365 ? 'M j' : 'M Y'));
            $values[] = $raw->get($key, 0);
            $days <= 31 ? $current->addDay() : ($days <= 365 ? $current->addWeek() : $current->addMonth());
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function buildPatientTrend(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end);
        $fmt  = $days <= 31 ? '%Y-%m-%d' : ($days <= 365 ? '%Y-%u' : '%Y-%m');

        $raw = Patient::whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(created_at, '{$fmt}') as period, COUNT(*) as cnt")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('cnt', 'period');

        $labels = []; $values = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->format($days <= 31 ? 'Y-m-d' : ($days <= 365 ? 'Y-W' : 'Y-m'));
            $labels[] = $current->format($days <= 31 ? 'M j' : ($days <= 365 ? 'M j' : 'M Y'));
            $values[] = $raw->get($key, 0);
            $days <= 31 ? $current->addDay() : ($days <= 365 ? $current->addWeek() : $current->addMonth());
        }

        return ['labels' => $labels, 'values' => $values];
    }
}