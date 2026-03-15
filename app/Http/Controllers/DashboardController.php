<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

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

        return view('dashboard.admin', compact('stats', 'recentAppointments', 'activityLogs'));
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