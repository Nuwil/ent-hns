<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $search     = $request->get('search');
        $sortBy     = $request->get('sort', 'name');        // name|age|visits|registered
        $filterGender    = $request->get('gender');         // male|female|other
        $filterBloodType = $request->get('blood_type');

        $query = Patient::query()->withCount('visits');

        // ── Doctor scoping ─────────────────────────────────────────
        // Regular doctors see patients they:
        //   (a) created, OR
        //   (b) are assigned to via an appointment, OR
        //   (c) have visited
        // Head doctors, admins, and secretaries see everyone.
        if ($user->isDoctor() && !$user->isHeadDoctor()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('appointments', fn($aq) => $aq->where('doctor_id', $user->id))
                  ->orWhereHas('visits', fn($vq) => $vq->where('doctor_id', $user->id));
            });
        }

        // ── Search ─────────────────────────────────────────────────
        $query->when($search, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('occupation', 'like', "%{$search}%");
            });
        });

        // ── Filters ────────────────────────────────────────────────
        $query->when($filterGender, fn($q, $v) => $q->where('gender', $v));
        $query->when($filterBloodType, fn($q, $v) => $q->where('blood_type', $v));

        // ── Sorting ────────────────────────────────────────────────
        match ($sortBy) {
            'age'        => $query->orderBy('date_of_birth', 'desc'), // youngest first
            'age_desc'   => $query->orderBy('date_of_birth', 'asc'),  // oldest first
            'visits'     => $query->orderByDesc('visits_count'),
            'registered' => $query->orderByDesc('created_at'),
            default      => $query->orderBy('last_name')->orderBy('first_name'),
        };

        $patients = $query->paginate(15)->withQueryString();

        return view('patients.index', compact('patients', 'search', 'sortBy', 'filterGender', 'filterBloodType'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female,other',
            'phone'          => 'required|string|max:50',
            'occupation'     => 'nullable|string|max:255',
            'province'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:500',
            'allergies'      => 'nullable|string|max:1000',
            'insurance_info' => 'nullable|string|max:500',
            'medical_history'=> 'nullable|string|max:3000',
        ]);

        $data['created_by'] = Auth::id();
        $patient = Patient::create($data);

        ActivityLog::log(
            action:      'patient.created',
            description: "Added new patient: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.patients.show", $patient)
            ->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient)
    {
        $user = Auth::user();

        // ── Access control ──────────────────────────────────────────
        // Regular doctors can only view patients they are assigned to
        // (via appointment), have visited, or created themselves.
        if (!$user->canAccessPatient($patient)) {
            abort(403, "You do not have access to this patient's records.");
        }

        // ── Scoped visit loading ────────────────────────────────────
        // Regular doctors only see THEIR OWN visits with this patient.
        // Admins, secretaries, and head doctors see all visits.
        if ($user->isDoctor() && !$user->isHeadDoctor()) {
            $patient->load([
                'appointments.doctor',
                'visits' => fn($q) => $q->where('doctor_id', $user->id)->with('doctor'),
            ]);
        } else {
            $patient->load([
                'visits.doctor',
                'appointments.doctor',
            ]);
        }

        $doctors = User::where('role', 'doctor')->get(['id', 'full_name']);

        // Check if there's a pending visit (accepted appointment waiting for visit entry)
        $pendingVisit = $patient->appointments()
            ->accepted()
            ->latest()
            ->first();

        // Let the view know if visits are doctor-scoped (to show a notice)
        $visitsScopedToDoctor = $user->isDoctor() && !$user->isHeadDoctor();

        return view('patients.show', compact('patient', 'doctors', 'pendingVisit', 'visitsScopedToDoctor'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female,other',
            'phone'          => 'required|string|max:50',
            'occupation'     => 'nullable|string|max:255',
            'province'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:500',
            'allergies'      => 'nullable|string|max:1000',
            'insurance_info' => 'nullable|string|max:500',
            'medical_history'=> 'nullable|string|max:3000',
        ]);

        $patient->update($data);

        ActivityLog::log(
            action:      'patient.updated',
            description: "Updated patient record: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.patients.show", $patient)
            ->with('toast_success', 'Patient updated successfully.');
    }

    public function addNote(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'note_text' => 'required|string|max:2000',
        ]);

        // Decode existing notes (support legacy plain text)
        $existing = [];
        if ($patient->notes) {
            $decoded = json_decode($patient->notes, true);
            $existing = is_array($decoded) ? $decoded : [
                ['text' => $patient->notes, 'author' => 'Legacy', 'created_at' => now('Asia/Manila')->toDateString()]
            ];
        }

        // Duplicate prevention — same author, same text, same day
        $today      = now('Asia/Manila')->toDateString();
        $noteText   = trim($data['note_text']);
        $authorName = Auth::user()->full_name;
        foreach ($existing as $note) {
            $noteDate = isset($note['created_at'])
                ? \Carbon\Carbon::parse($note['created_at'])->toDateString()
                : '';
            if ($noteDate === $today
                && trim($note['text'] ?? '') === $noteText
                && ($note['author'] ?? '') === $authorName) {
                return redirect()
                    ->route(Auth::user()->role . '.patients.show', $patient)
                    ->with('toast_error', 'This note was already added today.');
            }
        }

        // Append new note — store date only (PHT)
        $existing[] = [
            'text'       => $noteText,
            'author'     => $authorName,
            'role'       => Auth::user()->role,
            'created_at' => $today, // date only, PHT
        ];

        $patient->update(['notes' => json_encode($existing)]);

        ActivityLog::log(
            action:      'patient.note_added',
            description: "Added note to patient: {$patient->full_name}",
            severity:    'info',
            subject:     $patient,
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.patients.show", $patient)
            ->with('toast_success', 'Note added successfully.');
    }
    public function destroy(Patient $patient)
    {
        // Secretaries are not allowed to delete patient records
        if (Auth::user()->isSecretary()) {
            abort(403, 'Secretaries cannot delete patient records.');
        }

        $finalizedVisits = $patient->visits()->where('status', 'finalized')->count();
        if ($finalizedVisits > 0) {
            return back()->with('toast_error', "Cannot delete {$patient->full_name} — they have {$finalizedVisits} finalized visit record(s). Archive instead.");
        }

        $name = $patient->full_name;
        $patient->delete();

        ActivityLog::log(
            action:      'patient.deleted',
            description: "Deleted patient record: {$name}",
            severity:    'warning',
        );

        $role = Auth::user()->role;
        return redirect()
            ->route("{$role}.patients.index")
            ->with('toast_success', "{$name} has been removed from the registry.");
    }
}