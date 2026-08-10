<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * One method per role (admin/secretary/doctor), each returning that
 * role's landing-page view with the KPIs and recent-activity widgets
 * relevant to them. Kept as three separate methods rather than one
 * parameterized method because each role's dashboard pulls a genuinely
 * different set of data (e.g. admin sees clinic-wide stats, doctor sees
 * only their own patient queue).
 */
class DashboardController extends Controller
{
    public function admin()
    {
        $stats = [
            'total_patients'       => Patient::count(),
            'today_appointments'   => Appointment::today()->count(),
            'pending_appointments' => Appointment::pending()->count(),
            'total_visits'         => Visit::count(),
        ];

        $recentAppointments = Appointment::with('patient', 'doctor')
            ->latest()->take(5)->get();

        $activityLogs = ActivityLog::with('user')
            ->latest()->take(15)->get();

        $predictive = $this->buildPredictiveAnalytics();

        return view('dashboard.admin', compact('stats', 'recentAppointments', 'activityLogs', 'predictive'));
    }

    public function secretary()
    {
        $stats = [
            'total_patients'       => Patient::count(),
            'today_appointments'   => Appointment::today()->count(),
            'pending_appointments' => Appointment::pending()->count(),
            'upcoming_this_week'   => Appointment::upcoming()
                ->whereBetween('scheduled_at', [now(), now()->endOfWeek()])
                ->count(),
        ];

        $todayAppointments = Appointment::with('patient', 'doctor')
            ->today()->orderBy('scheduled_at')->get();

        $pendingAppointments = Appointment::with('patient')
            ->pending()->upcoming()->take(10)->get();

        $recentAppointments = Appointment::with('patient', 'doctor')
            ->latest()->take(15)->get();

        return view('dashboard.secretary', compact(
            'stats', 'todayAppointments', 'pendingAppointments', 'recentAppointments'
        ));
    }

    private function buildPredictiveAnalytics(): array
    {
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd   = now()->endOfMonth();
        $prevMonthStart = now()->subMonth()->startOfMonth();
        $prevMonthEnd   = now()->subMonth()->endOfMonth();

        $totalThisMonth = Visit::whereBetween('visited_at', [$thisMonthStart, $thisMonthEnd])->count();
        $totalPrevMonth = Visit::whereBetween('visited_at', [$prevMonthStart, $prevMonthEnd])->count();

        // Top 5 chief complaints this month with counts
        $thisMonthComplaints = Visit::whereBetween('visited_at', [$thisMonthStart, $thisMonthEnd])
            ->whereNotNull('chief_complaint')
            ->where('chief_complaint', '!=', '')
            ->selectRaw('chief_complaint, COUNT(*) as cnt')
            ->groupBy('chief_complaint')
            ->orderByDesc('cnt')
            ->take(5)
            ->pluck('cnt', 'chief_complaint');

        // Same complaints last month for trend
        $prevMonthComplaints = Visit::whereBetween('visited_at', [$prevMonthStart, $prevMonthEnd])
            ->whereNotNull('chief_complaint')
            ->where('chief_complaint', '!=', '')
            ->whereIn('chief_complaint', $thisMonthComplaints->keys())
            ->selectRaw('chief_complaint, COUNT(*) as cnt')
            ->groupBy('chief_complaint')
            ->pluck('cnt', 'chief_complaint');

        $complaints = $thisMonthComplaints->map(function ($cnt, $complaint) use ($prevMonthComplaints, $totalThisMonth) {
            $prev  = $prevMonthComplaints->get($complaint, 0);
            $trend = $cnt > $prev ? 'up' : ($cnt < $prev ? 'down' : 'same');
            return [
                'complaint' => $complaint,
                'count'     => $cnt,
                'prev'      => $prev,
                'pct'       => $totalThisMonth > 0 ? round($cnt / $totalThisMonth * 100, 1) : 0,
                'trend'     => $trend,
            ];
        })->values();

        return [
            'complaints'    => $complaints,
            'totalThisMonth' => $totalThisMonth,
            'totalPrevMonth' => $totalPrevMonth,
            'month'         => now()->format('F Y'),
        ];
    }

    public function doctor()
    {
        $doctor = Auth::user();

        $stats = [
            'my_today_appointments' => Appointment::today()
                ->where('doctor_id', $doctor->id)->count(),
            'my_patients'           => Visit::where('doctor_id', $doctor->id)
                ->distinct('patient_id')->count('patient_id'),
            'pending_appointments'  => Appointment::pending()
                ->where('doctor_id', $doctor->id)->count(),
            'visits_this_month'     => Visit::where('doctor_id', $doctor->id)
                ->whereMonth('visited_at', now()->month)->count(),
        ];

        $myAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->today()->orderBy('scheduled_at')->get();

        $recentVisits = Visit::with('patient')
            ->where('doctor_id', $doctor->id)
            ->latest('visited_at')->take(5)->get();

        $recentAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->latest('scheduled_at')
            ->take(8)
            ->get();

        return view('dashboard.doctor', compact(
            'stats', 'myAppointments', 'recentVisits', 'recentAppointments'
        ));
    }
}