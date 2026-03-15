<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();
        $doctorId = $doctor->id;

        // ── Summary KPIs ──────────────────────────────────────────────
        $totalPatients   = Visit::where('doctor_id', $doctorId)->distinct('patient_id')->count('patient_id');
        $visitsThisMonth = Visit::where('doctor_id', $doctorId)->whereMonth('visited_at', now()->month)->whereYear('visited_at', now()->year)->count();
        $visitsLastMonth = Visit::where('doctor_id', $doctorId)->whereMonth('visited_at', now()->subMonth()->month)->whereYear('visited_at', now()->subMonth()->year)->count();
        $visitGrowth     = $visitsLastMonth > 0 ? round((($visitsThisMonth - $visitsLastMonth) / $visitsLastMonth) * 100) : null;

        $appointmentStats = Appointment::where('doctor_id', $doctorId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $avgVisitsPerPatient = $totalPatients > 0
            ? round(Visit::where('doctor_id', $doctorId)->count() / $totalPatients, 1)
            : 0;

        // ── Visits Per Month — last 12 months ────────────────────────
        $visitsPerMonth = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $key   = $date->format('Y-m');
            $label = $date->format('M Y');
            $count = Visit::where('doctor_id', $doctorId)
                ->whereYear('visited_at', $date->year)
                ->whereMonth('visited_at', $date->month)
                ->count();
            $visitsPerMonth[$label] = $count;
        }

        // ── Predictive: Linear Regression Forecast (next 3 months) ───
        // Uses least squares regression on the last 12 months of visit counts
        $yValues = $visitsPerMonth->values()->toArray();
        $n       = count($yValues);
        $xValues = range(0, $n - 1);

        $sumX  = array_sum($xValues);
        $sumY  = array_sum($yValues);
        $sumXY = array_sum(array_map(fn($x, $y) => $x * $y, $xValues, $yValues));
        $sumX2 = array_sum(array_map(fn($x) => $x * $x, $xValues));

        $slope     = ($n * $sumXY - $sumX * $sumY) / max($n * $sumX2 - $sumX ** 2, 1);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecastLabels = [];
        $forecastValues = [];
        for ($i = 1; $i <= 3; $i++) {
            $forecastLabels[] = now()->addMonths($i)->format('M Y');
            $forecastValues[] = max(0, round($intercept + $slope * ($n - 1 + $i)));
        }

        // ── ENT Classification Trend (last 6 months) ─────────────────
        $entTrend = Visit::where('doctor_id', $doctorId)
            ->where('visited_at', '>=', now()->subMonths(6))
            ->whereNotNull('ent_classification')
            ->selectRaw('ent_classification, COUNT(*) as count')
            ->groupBy('ent_classification')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'ent_classification');

        // ── Top Diagnoses ─────────────────────────────────────────────
        $topDiagnoses = Visit::where('doctor_id', $doctorId)
            ->where('status', 'finalized')
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->selectRaw('diagnosis, COUNT(*) as count')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'diagnosis');

        // ── Busiest Days of Week ──────────────────────────────────────
        $busiestDays = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->selectRaw('DAYOFWEEK(scheduled_at) as dow, COUNT(*) as count')
            ->groupBy('dow')
            ->orderBy('dow')
            ->pluck('count', 'dow');

        $dayNames  = ['', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dayData   = [];
        for ($i = 1; $i <= 7; $i++) {
            $dayData[$dayNames[$i]] = $busiestDays->get($i, 0);
        }

        // ── Busiest Hours ─────────────────────────────────────────────
        $busiestHours = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->selectRaw('HOUR(scheduled_at) as hr, COUNT(*) as count')
            ->groupBy('hr')
            ->orderBy('hr')
            ->pluck('count', 'hr');

        $hourData = [];
        for ($h = 7; $h <= 18; $h++) {
            $label           = Carbon::createFromTime($h)->format('g A');
            $hourData[$label] = $busiestHours->get($h, 0);
        }

        // ── Patient Return Rate ───────────────────────────────────────
        $returningPatients = Visit::where('doctor_id', $doctorId)
            ->select('patient_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('patient_id')
            ->havingRaw('visit_count > 1')
            ->count();

        $returnRate = $totalPatients > 0
            ? round(($returningPatients / $totalPatients) * 100)
            : 0;

        // ── Age Group Distribution ────────────────────────────────────
        $ageGroups = Patient::whereHas('visits', fn($q) => $q->where('doctor_id', $doctorId))
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 13  THEN 'Child (0–12)'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18  THEN 'Teen (13–17)'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 30  THEN 'Young Adult (18–29)'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 45  THEN 'Adult (30–44)'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 60  THEN 'Middle-aged (45–59)'
                    ELSE 'Senior (60+)'
                END as age_group,
                COUNT(*) as count
            ")
            ->groupBy('age_group')
            ->orderBy('count', 'desc')
            ->pluck('count', 'age_group');

        // ── Gender Breakdown ──────────────────────────────────────────
        $genderBreakdown = Patient::whereHas('visits', fn($q) => $q->where('doctor_id', $doctorId))
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // ── No-show / Cancellation Rate ───────────────────────────────
        $totalAppts     = $appointmentStats->sum();
        $cancelledAppts = $appointmentStats->get('cancelled', 0);
        $cancelRate     = $totalAppts > 0 ? round(($cancelledAppts / $totalAppts) * 100) : 0;

        // ── Follow-up Compliance ──────────────────────────────────────
        $visitsWithFollowUp = Visit::where('doctor_id', $doctorId)
            ->where('status', 'finalized')
            ->whereNotNull('follow_up_date')
            ->count();
        $totalFinalized = Visit::where('doctor_id', $doctorId)->where('status', 'finalized')->count();
        $followUpRate   = $totalFinalized > 0 ? round(($visitsWithFollowUp / $totalFinalized) * 100) : 0;

        // ── Predictive Insight Messages ───────────────────────────────
        $insights = $this->generateInsights(
            slope: $slope,
            forecastValues: $forecastValues,
            forecastLabels: $forecastLabels,
            cancelRate: $cancelRate,
            returnRate: $returnRate,
            topDay: collect($dayData)->sortDesc()->keys()->first(),
            topHour: collect($hourData)->sortDesc()->keys()->first(),
            topEntClass: $entTrend->keys()->first(),
            visitsThisMonth: $visitsThisMonth,
            visitsLastMonth: $visitsLastMonth,
        );

        return view('analytics.index', compact(
            // KPIs
            'totalPatients', 'visitsThisMonth', 'visitsLastMonth', 'visitGrowth',
            'appointmentStats', 'avgVisitsPerPatient', 'returnRate', 'cancelRate', 'followUpRate',
            // Charts
            'visitsPerMonth', 'forecastLabels', 'forecastValues',
            'entTrend', 'topDiagnoses', 'genderBreakdown', 'ageGroups',
            'dayData', 'hourData',
            // Insights
            'insights',
        ));
    }

    private function generateInsights(
        float $slope,
        array $forecastValues,
        array $forecastLabels,
        int   $cancelRate,
        int   $returnRate,
        ?string $topDay,
        ?string $topHour,
        ?string $topEntClass,
        int   $visitsThisMonth,
        int   $visitsLastMonth,
    ): array {
        $insights = [];

        // Volume trend
        if ($slope > 0.5) {
            $insights[] = [
                'type'  => 'success',
                'icon'  => 'bi-graph-up-arrow',
                'title' => 'Patient Volume Growing',
                'text'  => "Your caseload is trending upward. Forecast shows approximately {$forecastValues[0]} visits in {$forecastLabels[0]}. Consider scheduling extra slots to accommodate demand.",
            ];
        } elseif ($slope < -0.5) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => 'bi-graph-down-arrow',
                'title' => 'Declining Visit Volume',
                'text'  => "Visit count has been declining over the past months. Forecast projects around {$forecastValues[0]} visits in {$forecastLabels[0]}. This may reflect seasonal patterns or appointment availability.",
            ];
        } else {
            $insights[] = [
                'type'  => 'info',
                'icon'  => 'bi-activity',
                'title' => 'Stable Patient Volume',
                'text'  => "Your visit volume is consistent. Forecast predicts approximately {$forecastValues[0]} visits in {$forecastLabels[0]}, suggesting steady patient demand.",
            ];
        }

        // Cancellation rate
        if ($cancelRate > 25) {
            $insights[] = [
                'type'  => 'danger',
                'icon'  => 'bi-calendar-x',
                'title' => 'High Cancellation Rate',
                'text'  => "Your cancellation rate is {$cancelRate}%, which is above the typical 15–20% threshold. Consider implementing appointment reminder messages to reduce no-shows.",
            ];
        } elseif ($cancelRate > 0) {
            $insights[] = [
                'type'  => 'success',
                'icon'  => 'bi-calendar-check',
                'title' => 'Low Cancellation Rate',
                'text'  => "Your cancellation rate of {$cancelRate}% is within healthy range, indicating good patient commitment to scheduled appointments.",
            ];
        }

        // Return rate
        if ($returnRate >= 60) {
            $insights[] = [
                'type'  => 'success',
                'icon'  => 'bi-arrow-repeat',
                'title' => 'High Patient Retention',
                'text'  => "{$returnRate}% of your patients return for follow-up visits, which reflects strong continuity of care and patient trust.",
            ];
        } elseif ($returnRate < 30 && $returnRate > 0) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => 'bi-person-dash',
                'title' => 'Low Return Rate',
                'text'  => "Only {$returnRate}% of patients return. Prescribing structured follow-up plans may improve long-term patient outcomes and retention.",
            ];
        }

        // Peak scheduling insight
        if ($topDay && $topHour) {
            $insights[] = [
                'type'  => 'info',
                'icon'  => 'bi-clock-history',
                'title' => 'Peak Schedule Insight',
                'text'  => "Your busiest appointment slot is {$topDay}s around {$topHour}. You may want to protect this window or allocate extra capacity for complex cases.",
            ];
        }

        // ENT classification insight
        if ($topEntClass) {
            $insights[] = [
                'type'  => 'info',
                'icon'  => 'bi-ear',
                'title' => 'Dominant Case Type',
                'text'  => "'{$topEntClass}' accounts for the most cases in the past 6 months. Ensuring your diagnostic toolkit and supplies for this category are well-stocked is recommended.",
            ];
        }

        // Month-over-month
        if ($visitsLastMonth > 0 && $visitsThisMonth > 0) {
            $diff = $visitsThisMonth - $visitsLastMonth;
            if ($diff > 0) {
                $insights[] = [
                    'type'  => 'success',
                    'icon'  => 'bi-arrow-up-circle',
                    'title' => 'Month-over-Month Increase',
                    'text'  => "You've seen {$diff} more patient(s) this month compared to last month — a positive sign of growing clinic activity.",
                ];
            }
        }

        return $insights;
    }
}