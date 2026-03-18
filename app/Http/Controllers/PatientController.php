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
        $search = $request->get('search');

        $patients = Patient::query()
            ->when($search, function ($q, $search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->withCount('visits')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
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
            'phone'          => 'required|string|max:20',
            'occupation'     => 'nullable|string|max:150',
            'province'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:500',
            'allergies'      => 'nullable|string|max:1000',
            'insurance_info' => 'nullable|string|max:500',
            'medical_history'=> 'nullable|string|max:3000',
        ]);

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
        $patient->load([
            'visits.doctor',
            'appointments.doctor',
        ]);

        $doctors = User::where('role', 'doctor')->get(['id', 'full_name']);

        // Check if there's a pending visit (accepted appointment waiting for visit entry)
        $pendingVisit = $patient->appointments()
            ->accepted()
            ->latest()
            ->first();

        return view('patients.show', compact('patient', 'doctors', 'pendingVisit'));
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
            'phone'          => 'required|string|max:20',
            'occupation'     => 'nullable|string|max:150',
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