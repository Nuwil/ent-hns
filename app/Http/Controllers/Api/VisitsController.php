<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientVisit;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VisitsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $patientId = $request->get('patient_id');
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);

            $query = PatientVisit::query();

            if ($patientId) {
                $query->where('patient_id', $patientId);
            }

            $total = $query->count();
            $visits = $query->with(['patient', 'doctor'])
                ->orderBy('visit_date', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => [
                    'visits' => $visits->items(),
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Get the current user's role - first check from request, then from session
            $userRole = $request->get('user_role');
            $user = null;
            
            if (!$userRole) {
                $userId = session('user_id');
                $user = $userId ? User::find($userId) : null;
                $userRole = $user ? $user->role : 'viewer';
            } else {
                // If user_role is in request, try to get user from session for audit trail
                $userId = session('user_id');
                $user = $userId ? User::find($userId) : null;
            }

            // Determine validation rules based on user role
            if ($userRole === 'secretary') {
                // Secretaries cannot submit doctor-only fields
                $validationRules = [
                    'patient_id' => 'required|exists:patients,id',
                    'appointment_id' => 'nullable|exists:appointments,id',
                    'visit_date' => 'required|date_format:Y-m-d',
                    'visit_type' => 'required|string|max:100',
                    'ent_type' => 'required|in:ear,nose,throat,head_neck_tumor,lifestyle_medicine,misc',
                    'chief_complaint' => 'required|string|max:1000',
                    'history' => 'nullable|string',
                    'physical_exam' => 'nullable|string',
                    'diagnosis' => 'nullable|string|max:1000',
                    'treatment_plan' => 'nullable|string',
                    'prescription' => 'nullable|string',
                    'notes' => 'nullable|string',
                    'blood_pressure' => 'nullable|string|max:50',
                    'temperature' => 'nullable|numeric',
                    'pulse_rate' => 'nullable|integer',
                    'respiratory_rate' => 'nullable|integer',
                    'oxygen_saturation' => 'nullable|integer|between:0,100',
                    'height' => 'nullable|numeric',
                    'weight' => 'nullable|numeric',
                    'vitals_notes' => 'nullable|string',
                    'user_role' => 'nullable|string',
                ];
            } else {
                // Doctors have access to all fields
                $validationRules = [
                    'patient_id' => 'required|exists:patients,id',
                    'appointment_id' => 'nullable|exists:appointments,id',
                    'visit_date' => 'required|date_format:Y-m-d',
                    'visit_type' => 'required|string|max:100',
                    'ent_type' => 'required|in:ear,nose,throat,head_neck_tumor,lifestyle_medicine,misc',
                    'chief_complaint' => 'required|string|max:1000',
                    'history' => 'nullable|string',
                    'physical_exam' => 'nullable|string',
                    'diagnosis' => 'required|string|max:1000',
                    'treatment_plan' => 'nullable|string',
                    'prescription' => 'nullable|string',
                    'notes' => 'nullable|string',
                    'blood_pressure' => 'nullable|string|max:50',
                    'temperature' => 'nullable|numeric',
                    'pulse_rate' => 'nullable|integer',
                    'respiratory_rate' => 'nullable|integer',
                    'oxygen_saturation' => 'nullable|integer|between:0,100',
                    'height' => 'nullable|numeric',
                    'weight' => 'nullable|numeric',
                    'vitals_notes' => 'nullable|string',
                    'user_role' => 'nullable|string',
                ];
            }

            $validated = $request->validate($validationRules, [
                'patient_id.required' => 'Patient is required',
                'visit_date.required' => 'Visit date is required',
                'visit_type.required' => 'Visit type is required',
                'ent_type.required' => 'ENT classification is required',
                'chief_complaint.required' => 'Chief complaint is required',
                'diagnosis.required' => 'Diagnosis is required for doctors',
            ]);

            // Enforce role-based restrictions at backend level
            if ($userRole === 'secretary') {
                // Verify secretaries didn't submit restricted fields with content
                if (!empty($validated['history']) || !empty($validated['physical_exam']) || !empty($validated['notes']) || !empty($validated['diagnosis']) || !empty($validated['treatment_plan']) || !empty($validated['prescription'])) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'authorization' => ['You do not have permission to submit medical information. Only doctors can submit diagnosis, treatment, prescription, history of present illness, physical examination findings, and notes.']
                        ]
                    ], 403);
                }
            }

            // Remove role field from validated data
            unset($validated['user_role']);

            // Get prescriptions array (will be processed after visit creation)
            $prescriptions = $request->get('prescriptions', []);

            // Convert date to datetime with current time
            $visitDateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['visit_date'])->setTime(now()->hour, now()->minute, now()->second);
            $validated['visit_date'] = $visitDateTime;

            // Store user info for audit trail
            if ($user) {
                $validated['doctor_id'] = $user->id;
                $validated['doctor_name'] = $user->full_name;
            }

            $visit = PatientVisit::create($validated);

            // Create prescription items if provided
            if (!empty($prescriptions) && is_array($prescriptions)) {
                foreach ($prescriptions as $prescription) {
                    $prescriptionData = [
                        'visit_id' => $visit->id,
                        'patient_id' => $visit->patient_id,
                        'medicine_id' => $prescription['medicine_id'] ?? null,
                        'medicine_name' => $prescription['medicine_name'] ?? null,
                        'instruction' => $prescription['instruction'] ?? null,
                        'doctor_id' => $user ? $user->id : null,
                    ];
                    
                    PrescriptionItem::create($prescriptionData);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Visit created',
                'data' => $visit
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $visit = PatientVisit::with(['patient', 'doctor', 'prescriptions'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $visit
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Visit not found'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $visit = PatientVisit::findOrFail($id);
            $visit->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Visit updated',
                'data' => $visit
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
