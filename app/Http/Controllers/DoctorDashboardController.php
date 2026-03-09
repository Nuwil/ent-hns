<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Analytics;
use Illuminate\Support\Facades\Schema;

class DoctorDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'doctor') {
            abort(403, 'Unauthorized access');
        }

        // Get doctor's statistics
        $totalAppointments = Appointment::where('doctor_id', $user->id)->count();
        $pendingAppointments = Appointment::where('doctor_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('doctor.dashboard', [
            'user' => $user,
            'totalAppointments' => $totalAppointments,
            'pendingAppointments' => $pendingAppointments,
        ]);
    }

    public function patients(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'doctor') {
            abort(403, 'Unauthorized access');
        }

        $search = $request->input('search', '');
        $query = Patient::query();

        if ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        // Get all patients (or filter by search)
        $patients = $query->paginate(20);

        return view('doctor.patients', ['user' => $user, 'patients' => $patients, 'search' => $search]);
    }

    public function patientProfile($patientId, Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'doctor') {
            abort(403, 'Unauthorized access');
        }

        $patient = Patient::find($patientId);
        
        if (!$patient) {
            abort(404, 'Patient not found');
        }

        // Show all appointments and visits for the patient
        $appointments = $patient->appointments()->get();
        $visits = $patient->visits()->get();

        return view('doctor.patient-profile', [
            'user' => $user,
            'patient' => $patient,
            'appointments' => $appointments,
            'visits' => $visits,
        ]);
    }

    public function appointments(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'doctor') {
            abort(403, 'Unauthorized access');
        }

        // Get paginated appointments for the table
        $appointments = Appointment::where('doctor_id', $user->id)
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->paginate(20);

        // Get appointments for the current month for the calendar
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->endOfMonth();
        
        $calendarAppointments = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$currentMonth, $nextMonth])
            ->with('patient')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_date, // Full datetime
                    'patient_name' => $appointment->patient ? $appointment->patient->first_name . ' ' . $appointment->patient->last_name : 'Unknown',
                    'appointment_type' => $appointment->type ?? 'General',
                    'duration' => $appointment->duration ?? 30,
                    'status' => $appointment->status ?? 'pending',
                    'notes' => $appointment->notes ?? '',
                    'doctor_name' => $user->full_name ?? 'N/A'
                ];
            });

        return view('doctor.appointments', [
            'user' => $user, 
            'appointments' => $appointments,
            'calendarAppointments' => $calendarAppointments
        ]);
    }

    public function analytics(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $user = User::find(session('user_id'));
        
        if (!$user || $user->role !== 'doctor') {
            session()->flush();
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in again.');
        }

        // Get time range filter
        $timeRange = $request->get('timeRange', 'this_month');
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        // Determine date range based on filter
        $dateRange = $this->getDateRange($timeRange, $fromDate, $toDate);

        // Get descriptive analytics (historical data)
        $descriptiveAnalytics = $this->getDescriptiveAnalytics($user->id, $dateRange);

        // Get predictive analytics (forecasting)
        $predictiveAnalytics = $this->getPredictiveAnalytics($user->id, $dateRange);

        // Get prescriptive analytics (recommendations)
        $prescriptiveAnalytics = $this->getPrescriptiveAnalytics($user->id, $dateRange);

        return view('doctor.analytics', [
            'user' => $user,
            'timeRange' => $timeRange,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'descriptiveAnalytics' => $descriptiveAnalytics,
            'predictiveAnalytics' => $predictiveAnalytics,
            'prescriptiveAnalytics' => $prescriptiveAnalytics,
        ]);
    }

    private function getDateRange($timeRange, $fromDate = null, $toDate = null)
    {
        $now = now();
        
        switch ($timeRange) {
            case 'today':
                return [
                    'from' => $now->copy()->startOfDay(),
                    'to' => $now->copy()->endOfDay(),
                ];
            case 'this_week':
                return [
                    'from' => $now->copy()->startOfWeek(),
                    'to' => $now->copy()->endOfWeek(),
                ];
            case 'this_month':
                return [
                    'from' => $now->copy()->startOfMonth(),
                    'to' => $now->copy()->endOfMonth(),
                ];
            case 'this_year':
                return [
                    'from' => $now->copy()->startOfYear(),
                    'to' => $now->copy()->endOfYear(),
                ];
            case 'custom':
                return [
                    'from' => $fromDate ? Carbon::parse($fromDate)->startOfDay() : $now->copy()->startOfMonth(),
                    'to' => $toDate ? Carbon::parse($toDate)->endOfDay() : $now->copy()->endOfMonth(),
                ];
            default:
                return [
                    'from' => $now->copy()->startOfMonth(),
                    'to' => $now->copy()->endOfMonth(),
                ];
        }
    }

    private function getDescriptiveAnalytics($doctorId, $dateRange)
    {
        $totalPatients = Patient::count();
        
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$dateRange['from'], $dateRange['to']])
            ->get();
        
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $pendingAppointments = $appointments->where('status', 'pending')->count();
        $confirmedAppointments = $appointments->where('status', 'confirmed')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();

        return [
            'total_patients' => $totalPatients,
            'completed_appointments' => $completedAppointments,
            'pending_appointments' => $pendingAppointments,
            'confirmed_appointments' => $confirmedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'total_appointments' => $appointments->count(),
            'appointment_status_distribution' => [
                'pending' => $pendingAppointments,
                'confirmed' => $confirmedAppointments,
                'completed' => $completedAppointments,
                'cancelled' => $cancelledAppointments,
            ],
        ];
    }

    private function getPredictiveAnalytics($doctorId, $dateRange)
    {
        // Get historical data to predict trends
        $historicalAppointments = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', '>=', now()->subMonths(3))
            ->get();

        // Calculate average appointments per day
        $avgAppointmentsPerDay = $historicalAppointments->count() / 90;

        // Predict appointments for the selected period
        $daysInRange = $dateRange['from']->diffInDays($dateRange['to']) + 1;
        $predictedAppointments = ceil($avgAppointmentsPerDay * $daysInRange);

        // Calculate completion rate for prediction
        $completionRate = $historicalAppointments->count() > 0 
            ? ($historicalAppointments->where('status', 'completed')->count() / $historicalAppointments->count()) * 100
            : 0;

        // Predict completed appointments
        $predictedCompletedAppointments = ceil($predictedAppointments * ($completionRate / 100));

        // Predict patient growth
        $currentMonthPatients = Patient::whereBetween('created_at', [
            now()->copy()->startOfMonth(),
            now()->copy()->endOfMonth()
        ])->count();
        
        $previousMonthPatients = Patient::whereBetween('created_at', [
            now()->copy()->subMonth()->startOfMonth(),
            now()->copy()->subMonth()->endOfMonth()
        ])->count();

        $patientGrowthRate = $previousMonthPatients > 0 
            ? (($currentMonthPatients - $previousMonthPatients) / $previousMonthPatients) * 100
            : 0;

        return [
            'predicted_appointments' => $predictedAppointments,
            'predicted_completed_appointments' => $predictedCompletedAppointments,
            'predicted_completion_rate' => round($completionRate, 2),
            'patient_growth_rate' => round($patientGrowthRate, 2),
            'avg_appointments_per_day' => round($avgAppointmentsPerDay, 2),
            'appointment_trend' => $this->getAppointmentTrend($doctorId),
        ];
    }

    private function getAppointmentTrend($doctorId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $count = Appointment::where('doctor_id', $doctorId)
                ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
                ->count();
            
            $months[$month->format('M Y')] = $count;
        }
        
        return $months;
    }

    private function getPrescriptiveAnalytics($doctorId, $dateRange)
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$dateRange['from'], $dateRange['to']])
            ->get();

        $recommendations = [];

        // Recommendation 1: High pending appointments
        $pendingCount = $appointments->where('status', 'pending')->count();
        if ($pendingCount > 5) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'High Pending Appointments',
                'message' => "You have $pendingCount pending appointments. Consider reaching out to patients to confirm their bookings.",
            ];
        }

        // Recommendation 2: Low completion rate
        $completionRate = $appointments->count() > 0 
            ? ($appointments->where('status', 'completed')->count() / $appointments->count()) * 100
            : 0;

        if ($completionRate < 75 && $appointments->count() > 0) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Below Average Completion Rate',
                'message' => "Your completion rate is {$completionRate}%. Try scheduling follow-ups and improving appointment reminders.",
            ];
        }

        // Recommendation 3: Cancellation rate
        $cancelledCount = $appointments->where('status', 'cancelled')->count();
        $cancellationRate = $appointments->count() > 0 
            ? ($cancelledCount / $appointments->count()) * 100
            : 0;

        if ($cancellationRate > 15) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'High Cancellation Rate',
                'message' => "Your cancellation rate is {$cancellationRate}%. Consider implementing better reminder systems.",
            ];
        }

        // Recommendation 4: Patient volume optimization
        $avgAppointmentsPerDay = $appointments->count() > 0 
            ? $appointments->count() / max(1, $dateRange['from']->diffInDays($dateRange['to']) + 1)
            : 0;

        if ($avgAppointmentsPerDay < 2) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Opportunity to Increase Patient Volume',
                'message' => "You have capacity for more appointments. Consider expanding your clinic hours.",
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'On Track',
                'message' => 'Your clinical performance metrics are looking good!',
            ];
        }

        return $recommendations;
    }

    public function storePatient(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'doctor') {
            abort(403, 'Unauthorized access');
        }

        // Validate the patient data
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:Male,Female,Other',
            'height' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:patients,email',
            'occupation' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_relationship' => 'required|string|max:50',
            'emergency_contact_phone' => 'required|string|max:20',
        ]);

        // Generate unique patient_id
        $patientId = 'PAT-' . time() . '-' . rand(1000, 9999);
        
        // Create the patient
        $patient = new Patient();
        $patient->patient_id = $patientId;
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->gender = $request->gender;
        $patient->height = $request->height;
        $patient->weight = $request->weight;
        $patient->bmi = $request->input('bmi', null);
        $patient->phone = $request->phone;
        $patient->email = $request->email;
        $patient->occupation = $request->occupation;
        $patient->country = $request->country;
        $patient->state = $request->state;
        $patient->city = $request->city;
        $patient->address = $request->address;
        $patient->allergies = $request->allergies;
        $patient->vaccine_history = $request->vaccine_history;
        $patient->emergency_contact_name = $request->emergency_contact_name;
        $patient->emergency_contact_relationship = $request->emergency_contact_relationship;
        $patient->emergency_contact_phone = $request->emergency_contact_phone;
        $patient->created_by = $user->id;
        $patient->save();

        return redirect()->route('doctor.patients')->with('status', 'Patient created successfully!');
    }
}
