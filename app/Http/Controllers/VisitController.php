<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    // ================================================================
    // SECRETARY — Create intake visit entry
    // Status: pending | Fields: chief complaint, ENT class, vitals
    // ================================================================

    public function storeIntake(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'appointment_id'     => 'nullable|exists:appointments,id',
            'doctor_id'          => 'nullable|exists:users,id',
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'blood_pressure'     => 'nullable|string|max:20',
            'weight'             => 'nullable|numeric|min:1|max:500',
            'height'             => 'nullable|numeric|min:30|max:250',
            'intake_notes'       => 'nullable|string|max:1000',
        ]);

        // Resolve doctor: explicit selection > appointment doctor > fallback
        $doctorId = $data['doctor_id'] ?? null;
        if (!$doctorId && !empty($data['appointment_id'])) {
            $doctorId = Appointment::find($data['appointment_id'])?->doctor_id;
        }
        if (!$doctorId) {
            $doctorId = $patient->appointments()->latest()->value('doctor_id') ?? Auth::id();
        }

        Visit::create([
            'patient_id'         => $patient->id,
            'doctor_id'          => $doctorId,
            'appointment_id'     => $data['appointment_id'] ?? null,
            'visited_at'         => now(),
            'ent_classification' => $data['ent_classification'],
            'chief_complaint'    => $data['chief_complaint'],
            'blood_pressure'     => $data['blood_pressure'] ?? null,
            'weight'             => $data['weight'] ?? null,
            'height'             => $data['height'] ?? null,
            'diagnosis'          => '',
            'notes'              => $data['intake_notes'] ?? null,
            'prescriptions'      => [],
            'recorded_by'        => 'secretary',
            'status'             => Visit::STATUS_PENDING,
        ]);

        ActivityLog::log(
            action:      'visit.intake_recorded',
            description: "Created intake visit for patient: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        return redirect()
            ->route('secretary.patients.show', $patient)
            ->with('toast_success', 'Visit entry created. Awaiting doctor completion.');
    }

    // ── Secretary update (only while still pending) ───────────────

    public function updateIntake(Request $request, Patient $patient, Visit $visit)
    {
        if (!$visit->secretaryCanEdit()) {
            return back()->with('toast_error', 'This visit can no longer be edited.');
        }

        $data = $request->validate([
            'doctor_id'          => 'nullable|exists:users,id',
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'blood_pressure'     => 'nullable|string|max:20',
            'weight'             => 'nullable|numeric|min:1|max:500',
            'height'             => 'nullable|numeric|min:30|max:250',
            'intake_notes'       => 'nullable|string|max:1000',
        ]);

        $visit->update([
            'doctor_id'          => $data['doctor_id'] ?? $visit->doctor_id,
            'ent_classification' => $data['ent_classification'],
            'chief_complaint'    => $data['chief_complaint'],
            'blood_pressure'     => $data['blood_pressure'] ?? null,
            'weight'             => $data['weight'] ?? null,
            'height'             => $data['height'] ?? null,
            'notes'              => $data['intake_notes'] ?? null,
        ]);

        return redirect()
            ->route('secretary.patients.show', $patient)
            ->with('toast_success', 'Visit intake updated.');
    }

    // ================================================================
    // DOCTOR — Create a full visit directly (no intake step)
    // Status: finalized immediately
    // ================================================================

    public function store(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'appointment_id'     => 'nullable|exists:appointments,id',
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'history'            => 'nullable|string|max:3000',
            'blood_pressure'     => 'nullable|string|max:20',
            'weight'             => 'nullable|numeric|min:1|max:500',
            'height'             => 'nullable|numeric|min:30|max:250',
            'physical_exam'      => 'nullable|string|max:3000',
            'diagnosis'          => 'required|string|max:1000',
            'plan_instructions'  => 'nullable|string|max:2000',
            'follow_up_date'     => 'nullable|date',
            'prescriptions'      => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit = Visit::create([
            'patient_id'          => $patient->id,
            'doctor_id'           => Auth::id(),
            'appointment_id'      => $data['appointment_id'] ?? null,
            'visited_at'          => now(),
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'     => $data['chief_complaint'],
            'history'             => $data['history'] ?? null,
            'blood_pressure'      => $data['blood_pressure'] ?? null,
            'weight'              => $data['weight'] ?? null,
            'height'              => $data['height'] ?? null,
            'physical_exam'       => $data['physical_exam'] ?? null,
            'diagnosis'           => $data['diagnosis'],
            'plan_instructions'   => $data['plan_instructions'] ?? null,
            'follow_up_date'      => $data['follow_up_date'] ?? null,
            'prescriptions'       => $prescriptions,
            'recorded_by'         => 'doctor',
            'status'              => Visit::STATUS_FINALIZED,
            'finalized_by'        => Auth::id(),
            'finalized_at'        => now(),
        ]);

        // Mark appointment as completed
        if (!empty($data['appointment_id'])) {
            Appointment::where('id', $data['appointment_id'])
                ->update(['status' => Appointment::STATUS_COMPLETED]);
        }

        // Auto-create follow-up appointment if follow_up_date was set
        if (!empty($data['follow_up_date'])) {
            Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => Auth::id(),
                'scheduled_at' => \Carbon\Carbon::parse($data['follow_up_date'])->startOfDay(),
                'reason'       => 'Follow-up: ' . $data['chief_complaint'],
                'status'       => Appointment::STATUS_PENDING,
                'notes'        => 'Auto-created from visit follow-up date.',
            ]);

            ActivityLog::log(
                action:      'appointment.booked',
                description: "Auto-booked follow-up appointment for: {$patient->full_name} on {$data['follow_up_date']}",
                severity:    'info',
                subject:     $patient,
            );
        }

        ActivityLog::log(
            action:      'visit.recorded',
            description: "Recorded and finalized visit for: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        $msg = 'Visit recorded and finalized.';
        if (!empty($data['follow_up_date'])) {
            $msg .= ' Follow-up appointment booked for ' . \Carbon\Carbon::parse($data['follow_up_date'])->format('M j, Y') . '.';
        }

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('toast_success', $msg);
    }

    // ================================================================
    // DOCTOR — Open & continue a pending/in-progress intake visit
    // ================================================================

    public function edit(Patient $patient, Visit $visit)
    {
        if ($visit->isLocked()) {
            return redirect()
                ->route('doctor.patients.show', $patient)
                ->with('toast_error', 'This visit has been finalized and is locked.');
        }

        // Mark as in_progress so secretary knows doctor is working on it
        if ($visit->isPending()) {
            $visit->update(['status' => Visit::STATUS_IN_PROGRESS]);
        }

        $entComplaints = $this->entComplaintsList();

        return view('patients.visit-edit', compact('patient', 'visit', 'entComplaints'));
    }

    // ── Doctor saves progress (not yet finalized) ─────────────────

    public function update(Request $request, Patient $patient, Visit $visit)
    {
        if ($visit->isLocked()) {
            return back()->with('toast_error', 'This visit is finalized and cannot be edited.');
        }

        $data = $request->validate([
            'ent_classification'  => 'required|string|max:100',
            'chief_complaint'     => 'required|string|max:500',
            'history'             => 'nullable|string|max:3000',
            'blood_pressure'      => 'nullable|string|max:20',
            'physical_exam'       => 'nullable|string|max:3000',
            'diagnosis'           => 'nullable|string|max:1000',
            'treatment_plan'      => 'nullable|string|max:3000',
            'plan_instructions'   => 'nullable|string|max:2000',
            'follow_up_date'      => 'nullable|date',
            'prescriptions'       => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit->update([
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'     => $data['chief_complaint'],
            'history'             => $data['history'] ?? null,
            'blood_pressure'      => $data['blood_pressure'] ?? $visit->blood_pressure,
            'physical_exam'       => $data['physical_exam'] ?? null,
            'diagnosis'           => $data['diagnosis'] ?? null,
            'treatment_plan'      => $data['treatment_plan'] ?? null,
            'plan_instructions'   => $data['plan_instructions'] ?? null,
            'follow_up_date'      => $data['follow_up_date'] ?? null,
            'prescriptions'       => $prescriptions,
            'doctor_id'           => Auth::id(),
            'status'              => Visit::STATUS_IN_PROGRESS,
        ]);

        // Auto-book follow-up if date was set and not already booked for this date
        if (!empty($data['follow_up_date'])) {
            $alreadyBooked = Appointment::where('patient_id', $patient->id)
                ->whereDate('scheduled_at', $data['follow_up_date'])
                ->where('notes', 'like', '%Auto-created from visit follow-up%')
                ->exists();

            if (!$alreadyBooked) {
                Appointment::create([
                    'patient_id'   => $patient->id,
                    'doctor_id'    => Auth::id(),
                    'scheduled_at' => \Carbon\Carbon::parse($data['follow_up_date']),
                    'reason'       => 'Follow-up: ' . $data['chief_complaint'],
                    'status'       => Appointment::STATUS_PENDING,
                    'notes'        => 'Auto-created from visit follow-up date.',
                ]);
            }
        }

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('toast_success', 'Visit progress saved. Follow-up appointment booked if date was set.');
    }

    // ================================================================
    // DOCTOR — Finalize visit → locked permanently
    // ================================================================

    public function finalize(Request $request, Patient $patient, Visit $visit)
    {
        if ($visit->isLocked()) {
            return back()->with('toast_error', 'This visit is already finalized.');
        }

        $data = $request->validate([
            'ent_classification'  => 'required|string|max:100',
            'chief_complaint'     => 'required|string|max:500',
            'history'             => 'nullable|string|max:3000',
            'blood_pressure'      => 'nullable|string|max:20',
            'physical_exam'       => 'nullable|string|max:3000',
            'diagnosis'           => 'required|string|max:1000',
            'treatment_plan'      => 'nullable|string|max:3000',
            'plan_instructions'   => 'nullable|string|max:2000',
            'follow_up_date'      => 'nullable|date',
            'prescriptions'       => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit->update([
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'     => $data['chief_complaint'],
            'history'             => $data['history'] ?? null,
            'blood_pressure'      => $data['blood_pressure'] ?? $visit->blood_pressure,
            'physical_exam'       => $data['physical_exam'] ?? null,
            'diagnosis'           => $data['diagnosis'],
            'treatment_plan'      => $data['treatment_plan'] ?? null,
            'plan_instructions'   => $data['plan_instructions'] ?? null,
            'follow_up_date'      => $data['follow_up_date'] ?? null,
            'prescriptions'       => $prescriptions,
            'doctor_id'           => Auth::id(),
            'status'              => Visit::STATUS_FINALIZED,
            'finalized_by'        => Auth::id(),
            'finalized_at'        => now(),
        ]);

        // Mark linked appointment as completed
        if ($visit->appointment_id) {
            Appointment::where('id', $visit->appointment_id)
                ->update(['status' => Appointment::STATUS_COMPLETED]);
        }

        // Auto-create follow-up appointment if follow_up_date was set
        if (!empty($data['follow_up_date'])) {
            Appointment::create([
                'patient_id'   => $patient->id,
                'doctor_id'    => Auth::id(),
                'scheduled_at' => \Carbon\Carbon::parse($data['follow_up_date'])->startOfDay(),
                'reason'       => 'Follow-up: ' . $visit->chief_complaint,
                'status'       => Appointment::STATUS_PENDING,
                'notes'        => 'Auto-created from visit follow-up date.',
            ]);

            ActivityLog::log(
                action:      'appointment.booked',
                description: "Auto-booked follow-up appointment for: {$patient->full_name} on {$data['follow_up_date']}",
                severity:    'info',
                subject:     $patient,
            );
        }

        ActivityLog::log(
            action:      'visit.finalized',
            description: "Finalized visit record for: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('toast_success', 'Visit finalized and locked permanently.');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function formatVitals(array $data): string
    {
        $lines = [];
        if (!empty($data['blood_pressure'])) $lines[] = "BP: {$data['blood_pressure']}";
        if (!empty($data['heart_rate']))     $lines[] = "HR: {$data['heart_rate']} bpm";
        if (!empty($data['temperature']))    $lines[] = "Temp: {$data['temperature']}°C";
        if (!empty($data['weight']))         $lines[] = "Weight: {$data['weight']} kg";
        if (!empty($data['allergies_note'])) $lines[] = "Allergies: {$data['allergies_note']}";
        if (!empty($data['intake_notes']))   $lines[] = "Notes: {$data['intake_notes']}";
        return implode(' | ', $lines);
    }

    public static function entComplaintsList(): array
    {
        return [
            'Head & Neck' => [
                'Difficulty opening mouth (Trismus)', 'Facial swelling',
                'Mouth sore / ulcer', 'Neck mass / lump', 'Neck pain',
                'Salivary gland swelling', 'Thyroid swelling',
            ],
            'Laryngology (Throat / Voice)' => [
                'Cough', 'Difficulty swallowing (Dysphagia)',
                'Globus sensation (lump in throat)', 'Hoarseness / Voice change',
                'Sore throat', 'Stridor / Noisy breathing', 'Throat clearing',
            ],
            'Otology (Ear)' => [
                'Ear discharge (Otorrhea)', 'Ear fullness / blocked ear',
                'Ear pain (Otalgia)', 'Foreign body in ear',
                'Hearing loss', 'Itchy ear',
                'Tinnitus (Ringing in ear)', 'Vertigo / Dizziness',
            ],
            'Rhinology (Nose)' => [
                'Facial pain / pressure', 'Foreign body in nose',
                'Loss of smell (Anosmia)', 'Nasal congestion / Blocked nose',
                'Nosebleed (Epistaxis)', 'Post-nasal drip',
                'Runny nose (Rhinorrhea)', 'Sneezing',
            ],
            'Others' => ['Others'],
        ];
    }

    /**
     * Maps a chief complaint back to its ENT classification automatically.
     */
    public static function ccToEntMap(): array
    {
        $map = [];
        foreach (self::entComplaintsList() as $cat => $complaints) {
            foreach ($complaints as $c) {
                $map[$c] = $cat;
            }
        }
        return $map;
    }
}