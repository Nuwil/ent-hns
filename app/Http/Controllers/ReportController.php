<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function data(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type'  => 'required|in:weekly,monthly',
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $start = \Carbon\Carbon::parse($request->start)->startOfDay();
        $end   = \Carbon\Carbon::parse($request->end)->endOfDay();

        if ($start->gt($end)) {
            return response()->json([
                'error' => 'The Start Date cannot be later than the End Date. Please select a valid date range.'
            ], 422);
        }

        // Previous period for comparison (same duration)
        $duration    = $start->diffInDays($end) + 1;
        $prevEnd     = $start->copy()->subDay()->endOfDay();
        $prevStart   = $prevEnd->copy()->subDays($duration - 1)->startOfDay();

        // ── Patients ──────────────────────────────────────────────
        $allPatientIds = Visit::whereBetween('visited_at', [$start, $end])
            ->distinct('patient_id')->pluck('patient_id');

        $newPatientIds = Patient::whereBetween('created_at', [$start, $end])
            ->pluck('id');

        $repeatPatientIds = $allPatientIds->diff($newPatientIds);

        $totalPatients  = $allPatientIds->count();
        $newPatients    = $newPatientIds->count();
        $repeatPatients = $repeatPatientIds->count();

        // ── Repeat Patient Leaderboard (any patient with >1 visit in period) ──
        $allVisitCounts = Visit::whereBetween('visited_at', [$start, $end])
            ->selectRaw('patient_id, COUNT(*) as visit_count')
            ->groupBy('patient_id')
            ->orderByDesc('visit_count')
            ->with('patient:id,first_name,last_name,gender,date_of_birth')
            ->get();

        $repeatLeaderboard = $allVisitCounts
            ->filter(fn($r) => $r->visit_count > 1)
            ->take(10)
            ->map(fn($r) => [
                'name'        => $r->patient?->full_name ?? 'Unknown',
                'gender'      => $r->patient?->gender ?? '—',
                'age'         => $r->patient?->date_of_birth?->age ?? '—',
                'visit_count' => $r->visit_count,
                'is_repeat'   => true,
            ]);

        $allPatientsTable = $allVisitCounts
            ->take(20)
            ->map(fn($r) => [
                'name'        => $r->patient?->full_name ?? 'Unknown',
                'gender'      => $r->patient?->gender ?? '—',
                'age'         => $r->patient?->date_of_birth?->age ?? '—',
                'visit_count' => $r->visit_count,
                'is_repeat'   => $r->visit_count > 1,
            ]);

        // ── Consultations ─────────────────────────────────────────
        $totalConsultations = Visit::whereBetween('visited_at', [$start, $end])->count();
        $prevConsultations  = Visit::whereBetween('visited_at', [$prevStart, $prevEnd])->count();
        $consultationGrowth = $prevConsultations > 0
            ? round((($totalConsultations - $prevConsultations) / $prevConsultations) * 100, 1)
            : null;

        // ── Chief Complaints ──────────────────────────────────────
        $complaints = Visit::whereBetween('visited_at', [$start, $end])
            ->whereNotNull('chief_complaint')
            ->where('chief_complaint', '!=', '')
            ->selectRaw('chief_complaint, COUNT(*) as cnt')
            ->groupBy('chief_complaint')
            ->orderByDesc('cnt')
            ->take(10)
            ->get()
            ->map(fn($r) => [
                'complaint' => $r->chief_complaint,
                'count'     => $r->cnt,
                'pct'       => $totalConsultations > 0
                    ? round($r->cnt / $totalConsultations * 100, 1) : 0,
            ]);

        // Trend vs previous period for top 5
        $prevComplaints = Visit::whereBetween('visited_at', [$prevStart, $prevEnd])
            ->whereNotNull('chief_complaint')
            ->where('chief_complaint', '!=', '')
            ->selectRaw('chief_complaint, COUNT(*) as cnt')
            ->groupBy('chief_complaint')
            ->pluck('cnt', 'chief_complaint');

        $complaintsWithTrend = $complaints->map(function ($c) use ($prevComplaints) {
            $prev = $prevComplaints->get($c['complaint'], 0);
            $c['trend'] = $c['count'] > $prev ? 'up' : ($c['count'] < $prev ? 'down' : 'same');
            $c['prev']  = $prev;
            return $c;
        });

        // ── Age Distribution ──────────────────────────────────────
        $patients = Patient::whereIn('id', $allPatientIds)->get(['date_of_birth']);
        $ageGroups = ['0-12' => 0, '13-17' => 0, '18-30' => 0, '31-50' => 0, '51-70' => 0, '71+' => 0];
        foreach ($patients as $p) {
            $age = $p->date_of_birth->age;
            if ($age <= 12)      $ageGroups['0-12']++;
            elseif ($age <= 17)  $ageGroups['13-17']++;
            elseif ($age <= 30)  $ageGroups['18-30']++;
            elseif ($age <= 50)  $ageGroups['31-50']++;
            elseif ($age <= 70)  $ageGroups['51-70']++;
            else                 $ageGroups['71+']++;
        }

        // ── Gender Distribution ───────────────────────────────────
        $genderDist = Patient::whereIn('id', $allPatientIds)
            ->selectRaw('gender, COUNT(*) as cnt')
            ->groupBy('gender')
            ->pluck('cnt', 'gender');

        // ── Appointments ──────────────────────────────────────────
        $apptStats = Appointment::whereBetween('scheduled_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $apptCompleted  = $apptStats->get('completed', 0);
        $apptCancelled  = $apptStats->get('cancelled', 0);
        $apptPending    = $apptStats->get('pending', 0);
        $apptTotal      = $apptStats->sum();

        // ── Recurring Illnesses (diagnoses) — monthly only ────────
        $recurringIllnesses = Visit::whereBetween('visited_at', [$start, $end])
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->selectRaw('diagnosis, COUNT(*) as cnt')
            ->groupBy('diagnosis')
            ->orderByDesc('cnt')
            ->take(8)
            ->get()
            ->map(fn($r) => ['diagnosis' => $r->diagnosis, 'count' => $r->cnt]);

        // ── Daily trend for chart ─────────────────────────────────
        $dailyVisits = Visit::whereBetween('visited_at', [$start, $end])
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day');

        $trendLabels = [];
        $trendValues = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            $trendLabels[] = $cur->format('M j');
            $trendValues[] = (int) $dailyVisits->get($cur->format('Y-m-d'), 0);
            $cur->addDay();
        }

        // ── Summary insights ──────────────────────────────────────
        $insights = $this->buildInsights(
            $totalPatients, $newPatients, $repeatPatients,
            $totalConsultations, $prevConsultations, $consultationGrowth,
            $complaintsWithTrend, $apptCancelled, $apptTotal
        );

        return response()->json([
            'period'             => ['start' => $start->format('F j, Y'), 'end' => $end->format('F j, Y')],
            'type'               => $request->type,
            'totalPatients'      => $totalPatients,
            'newPatients'        => $newPatients,
            'repeatPatients'     => $repeatPatients,
            'repeatLeaderboard'  => $repeatLeaderboard->values(),
            'allPatientsTable'   => $allPatientsTable->values(),
            'totalConsultations' => $totalConsultations,
            'prevConsultations'  => $prevConsultations,
            'consultationGrowth' => $consultationGrowth,
            'complaints'         => $complaintsWithTrend->values(),
            'ageGroups'          => $ageGroups,
            'genderDist'         => $genderDist,
            'apptCompleted'      => $apptCompleted,
            'apptCancelled'      => $apptCancelled,
            'apptPending'        => $apptPending,
            'apptTotal'          => $apptTotal,
            'recurringIllnesses' => $recurringIllnesses,
            'trend'              => ['labels' => $trendLabels, 'values' => $trendValues],
            'insights'           => $insights,
        ]);
    }

    private function buildInsights(
        int $total, int $new, int $repeat,
        int $consults, int $prevConsults, ?float $growth,
        $complaints, int $cancelled, int $apptTotal
    ): array {
        $insights = [];

        if ($consults === 0) {
            $insights[] = ['type' => 'info', 'text' => 'No consultations recorded for this period.'];
            return $insights;
        }

        // Growth insight
        if ($growth !== null) {
            if ($growth > 10) {
                $insights[] = ['type' => 'success', 'text' => "Consultation volume grew by {$growth}% compared to the previous period — strong patient demand."];
            } elseif ($growth < -10) {
                $insights[] = ['type' => 'warning', 'text' => "Consultation volume dropped by " . abs($growth) . "% vs previous period. Consider reviewing appointment availability."];
            } else {
                $insights[] = ['type' => 'info', 'text' => "Consultation volume is stable ({$growth}% change vs previous period)."];
            }
        }

        // Repeat patient insight
        $repeatPct = $total > 0 ? round($repeat / $total * 100) : 0;
        if ($repeatPct > 60) {
            $insights[] = ['type' => 'success', 'text' => "{$repeatPct}% of patients this period are returning patients — strong continuity of care."];
        } elseif ($repeatPct < 30 && $total > 5) {
            $insights[] = ['type' => 'info', 'text' => "{$new} new patients registered this period, indicating healthy patient acquisition."];
        }

        // Top complaint insight
        if ($complaints->count() > 0) {
            $top = $complaints->first();
            $insights[] = ['type' => 'info', 'text' => "Most common complaint: \"{$top['complaint']}\" ({$top['count']} cases, {$top['pct']}% of consultations)."];
        }

        // Cancellation insight
        $cancelPct = $apptTotal > 0 ? round($cancelled / $apptTotal * 100) : 0;
        if ($cancelPct > 20) {
            $insights[] = ['type' => 'warning', 'text' => "Appointment cancellation rate is {$cancelPct}% — consider patient reminder systems to reduce no-shows."];
        }

        return $insights;
    }
}
