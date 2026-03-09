<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;

class SecretaryDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'secretary') {
            abort(403, 'Unauthorized access');
        }

        // Get statistics for secretary dashboard
        $totalPatients = Patient::count();
        $totalAppointments = Appointment::count();
        $upcomingAppointments = Appointment::where('appointment_date', '>=', now())
            ->where('status', '!=', 'completed')
            ->count();

        return view('secretary.dashboard', [
            'user' => $user,
            'totalPatients' => $totalPatients,
            'totalAppointments' => $totalAppointments,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }

    public function patients(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'secretary') {
            abort(403, 'Unauthorized access');
        }

        $search = $request->input('search', '');
        $query = Patient::query();

        if ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $patients = $query->paginate(20);

        return view('secretary.patients', [
            'user' => $user,
            'patients' => $patients,
            'search' => $search,
        ]);
    }

    public function patientProfile($patientId, Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'secretary') {
            abort(403, 'Unauthorized access');
        }

        $patient = Patient::find($patientId);
        
        if (!$patient) {
            abort(404, 'Patient not found');
        }

        // Secretary has limited access - can only view, not edit
        $appointments = $patient->appointments()->get();
        $visits = $patient->visits()->get();

        return view('secretary.patient-profile', [
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
        
        if ($user->role !== 'secretary') {
            abort(403, 'Unauthorized access');
        }

        $filterStatus = $request->input('status', '');
        $query = Appointment::query()->with('patient', 'doctor');

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        // Get paginated appointments for the table
        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(20);

        // Get appointments for the current month for the calendar
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->endOfMonth();
        
        $calendarAppointments = Appointment::with('patient', 'doctor')
            ->whereBetween('appointment_date', [$currentMonth, $nextMonth])
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
                    'doctor_name' => $appointment->doctor ? $appointment->doctor->full_name : 'N/A'
                ];
            });

        return view('secretary.appointments', [
            'user' => $user,
            'appointments' => $appointments,
            'filterStatus' => $filterStatus,
            'calendarAppointments' => $calendarAppointments
        ]);
    }

    public function storePatient(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'secretary') {
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
        $patient->emergency_contact_phone = $request->emergency_contact_phone;
        $patient->created_by = $user->id;
        $patient->save();

        return redirect()->route('secretary.patients')->with('status', 'Patient created successfully!');
    }
}
