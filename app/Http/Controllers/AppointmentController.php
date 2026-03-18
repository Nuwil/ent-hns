<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $date   = $request->get('date');
        $role   = Auth::user()->role;

        $query = Appointment::with('patient', 'doctor')->latest('scheduled_at');

        if ($role === 'doctor') {
            $query->where('doctor_id', Auth::id());
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('scheduled_at', $date);
        }

        $appointments = $query->paginate(20)->withQueryString();
        $doctors      = User::where('role', 'doctor')->get(['id', 'full_name']);
        $patients     = Patient::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        // Calendar data
        $calMonth = (int) $request->get('cal_month', now()->month);
        $calYear  = (int) $request->get('cal_year',  now()->year);

        $calQuery = Appointment::with('patient', 'doctor')
            ->whereMonth('scheduled_at', $calMonth)
            ->whereYear('scheduled_at',  $calYear);

        if ($role === 'doctor') {
            $calQuery->where('doctor_id', Auth::id());
        }

        $calendarAppointments = $calQuery->get()->map(fn($a) => [
            'id'         => $a->id,
            'patient'    => $a->patient->full_name ?? '—',
            'phone'      => $a->patient->phone ?? '—',
            'doctor'     => $a->doctor->name ?? '—',
            'date'       => $a->scheduled_at->format('Y-m-d'),
            'time'       => $a->scheduled_at->format('H:i'),
            'reason'     => $a->reason,
            'status'     => $a->status,
            'patient_id' => $a->patient_id,
        ]);

        $isAdmin = $role === 'admin';

        return view('appointments.index', compact(
            'appointments', 'doctors', 'patients',
            'status', 'date',
            'calendarAppointments', 'calMonth', 'calYear',
            'isAdmin'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'doctor_id'    => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:today',
            'reason'       => 'required|string|max:500',
            'notes'        => 'nullable|string|max:1000',
        ]);

        // Always starts as pending regardless of who booked it
        $data['status'] = Appointment::STATUS_PENDING;

        Appointment::create($data);

        ActivityLog::log(
            action:      'appointment.booked',
            description: "Booked appointment for patient ID {$data['patient_id']}",
            severity:    'info',
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.appointments.index")
            ->with('toast_success', 'Appointment booked successfully. Status set to Pending.');
    }

    /**
     * DOCTOR ONLY — Confirm a pending appointment.
     * Redirects to patient profile to open Add Visit modal.
     */
    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => Appointment::STATUS_ACCEPTED]);

        ActivityLog::log(
            action:      'appointment.confirmed',
            description: "Confirmed appointment for {$appointment->patient->full_name}",
            severity:    'info',
            subject:     $appointment,
        );

        return redirect()
            ->route('doctor.patients.show', $appointment->patient_id)
            ->with('open_visit_modal', true)
            ->with('appointment_id', $appointment->id)
            ->with('toast_success', 'Appointment confirmed. You may now record a visit.');
    }

    /**
     * DOCTOR ONLY — Mark appointment as completed.
     */
    public function complete(Appointment $appointment)
    {
        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        ActivityLog::log(
            action:      'appointment.completed',
            description: "Marked appointment as completed for {$appointment->patient->full_name}",
            severity:    'info',
            subject:     $appointment,
        );

        return redirect()
            ->route('doctor.appointments.index')
            ->with('toast_success', 'Appointment marked as completed.');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        ActivityLog::log(
            action:      'appointment.cancelled',
            description: "Cancelled appointment for {$appointment->patient->full_name}",
            severity:    'warning',
            subject:     $appointment,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.appointments.index")
            ->with('toast_success', 'Appointment cancelled.');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'scheduled_at' => 'required|date|after:today',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $appointment->update([
            'scheduled_at' => $data['scheduled_at'],
            'notes'        => $data['notes'] ?? $appointment->notes,
            'status'       => Appointment::STATUS_PENDING,
        ]);

        ActivityLog::log(
            action:      'appointment.rescheduled',
            description: "Rescheduled appointment for {$appointment->patient->full_name}",
            severity:    'info',
            subject:     $appointment,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.appointments.index")
            ->with('toast_success', 'Appointment rescheduled successfully.');
    }

    public function reassign(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $appointment->update(['doctor_id' => $data['doctor_id']]);

        ActivityLog::log(
            action:      'appointment.reassigned',
            description: "Reassigned appointment for {$appointment->patient->full_name}",
            severity:    'info',
            subject:     $appointment,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.appointments.index")
            ->with('toast_success', 'Appointment reassigned successfully.');
    }
}