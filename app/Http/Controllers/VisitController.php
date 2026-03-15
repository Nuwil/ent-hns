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
            'appointment_id'   => 'nullable|exists:appointments,id',
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'  => 'required|string|max:500',
            'blood_pressure'   => 'nullable|string|max:20',
            'heart_rate'       => 'nullable|integer|min:20|max:300',
            'temperature'      => 'nullable|numeric|min:30|max:45',
            'weight'           => 'nullable|numeric|min:1|max:500',
            'allergies_note'   => 'nullable|string|max:500',
            'intake_notes'     => 'nullable|string|max:1000',
        ]);

        // Get doctor from linked appointment or null
        $doctorId = null;
        if (!empty($data['appointment_id'])) {
            $doctorId = Appointment::find($data['appointment_id'])?->doctor_id;
        }

        Visit::create([
            'patient_id'        => $patient->id,
            'doctor_id'         => $doctorId ?? $patient->appointments()->latest()->value('doctor_id') ?? Auth::id(),
            'appointment_id'    => $data['appointment_id'] ?? null,
            'visited_at'        => now(),
            'ent_classification' => $data['ent_classification'],
            'chief_complaint'   => $data['chief_complaint'],
            'diagnosis'         => '', // empty until doctor fills it
            'notes'             => $this->formatVitals($data),
            'prescriptions'     => [],
            'recorded_by'       => 'secretary',
            'status'            => Visit::STATUS_PENDING,
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
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'blood_pressure'     => 'nullable|string|max:20',
            'heart_rate'         => 'nullable|integer|min:20|max:300',
            'temperature'        => 'nullable|numeric|min:30|max:45',
            'weight'             => 'nullable|numeric|min:1|max:500',
            'allergies_note'     => 'nullable|string|max:500',
            'intake_notes'       => 'nullable|string|max:1000',
        ]);

        $visit->update([
            'ent_classification' => $data['ent_classification'],
            'chief_complaint'    => $data['chief_complaint'],
            'notes'              => $this->formatVitals($data),
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
            'history_of_illness' => 'nullable|string|max:3000',
            'exam_findings'      => 'nullable|string|max:3000',
            'diagnosis'          => 'required|string|max:1000',
            'treatment_plan'     => 'nullable|string|max:3000',
            'notes'              => 'nullable|string|max:2000',
            'follow_up_date'     => 'nullable|date|after:today',
            'prescriptions'      => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit = Visit::create([
            'patient_id'         => $patient->id,
            'doctor_id'          => Auth::id(),
            'appointment_id'     => $data['appointment_id'] ?? null,
            'visited_at'         => now(),
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'    => $data['chief_complaint'],
            'history_of_illness' => $data['history_of_illness'] ?? null,
            'exam_findings'      => $data['exam_findings'] ?? null,
            'diagnosis'          => $data['diagnosis'],
            'treatment_plan'     => $data['treatment_plan'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'follow_up_date'     => $data['follow_up_date'] ?? null,
            'prescriptions'      => $prescriptions,
            'recorded_by'        => 'doctor',
            'status'             => Visit::STATUS_FINALIZED,
            'finalized_by'       => Auth::id(),
            'finalized_at'       => now(),
        ]);

        // Mark appointment as completed
        if (!empty($data['appointment_id'])) {
            Appointment::where('id', $data['appointment_id'])
                ->update(['status' => Appointment::STATUS_COMPLETED]);
        }

        ActivityLog::log(
            action:      'visit.recorded',
            description: "Recorded and finalized visit for: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('toast_success', 'Visit recorded and finalized.');
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
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'history_of_illness' => 'nullable|string|max:3000',
            'exam_findings'      => 'nullable|string|max:3000',
            'diagnosis'          => 'nullable|string|max:1000',
            'treatment_plan'     => 'nullable|string|max:3000',
            'notes'              => 'nullable|string|max:2000',
            'follow_up_date'     => 'nullable|date',
            'prescriptions'      => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit->update([
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'     => $data['chief_complaint'],
            'history_of_illness'  => $data['history_of_illness'] ?? null,
            'exam_findings'       => $data['exam_findings'] ?? null,
            'diagnosis'           => $data['diagnosis'] ?? null,
            'treatment_plan'      => $data['treatment_plan'] ?? null,
            'notes'               => $data['notes'] ?? null,
            'follow_up_date'      => $data['follow_up_date'] ?? null,
            'prescriptions'       => $prescriptions,
            'doctor_id'           => Auth::id(),
            'status'              => Visit::STATUS_IN_PROGRESS,
        ]);

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('toast_success', 'Visit progress saved. Remember to finalize when complete.');
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
            'ent_classification' => 'required|string|max:100',
            'chief_complaint'    => 'required|string|max:500',
            'history_of_illness' => 'nullable|string|max:3000',
            'exam_findings'      => 'nullable|string|max:3000',
            'diagnosis'          => 'required|string|max:1000',
            'treatment_plan'     => 'nullable|string|max:3000',
            'notes'              => 'nullable|string|max:2000',
            'follow_up_date'     => 'nullable|date',
            'prescriptions'      => 'nullable|string',
        ]);

        $prescriptions = [];
        if (!empty($data['prescriptions'])) {
            $decoded = json_decode($data['prescriptions'], true);
            $prescriptions = is_array($decoded) ? $decoded : [];
        }

        $visit->update([
            'ent_classification'  => $data['ent_classification'],
            'chief_complaint'     => $data['chief_complaint'],
            'history_of_illness'  => $data['history_of_illness'] ?? null,
            'exam_findings'       => $data['exam_findings'] ?? null,
            'diagnosis'           => $data['diagnosis'],
            'treatment_plan'      => $data['treatment_plan'] ?? null,
            'notes'               => $data['notes'] ?? null,
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
            'Otology (Ear)' => [
                'Ear pain (Otalgia)', 'Hearing loss', 'Ear discharge (Otorrhea)',
                'Tinnitus (Ringing in ear)', 'Ear fullness / blocked ear',
                'Vertigo / Dizziness', 'Itchy ear', 'Foreign body in ear',
            ],
            'Rhinology (Nose)' => [
                'Nasal congestion / Blocked nose', 'Runny nose (Rhinorrhea)',
                'Nosebleed (Epistaxis)', 'Loss of smell (Anosmia)',
                'Facial pain / pressure', 'Post-nasal drip', 'Sneezing',
                'Foreign body in nose',
            ],
            'Laryngology (Throat / Voice)' => [
                'Sore throat', 'Hoarseness / Voice change',
                'Difficulty swallowing (Dysphagia)', 'Throat clearing',
                'Cough', 'Globus sensation (lump in throat)', 'Stridor / Noisy breathing',
            ],
            'Head & Neck' => [
                'Neck mass / lump', 'Neck pain', 'Facial swelling',
                'Mouth sore / ulcer', 'Difficulty opening mouth (Trismus)',
                'Salivary gland swelling', 'Thyroid swelling',
            ],
        ];
    }
}