<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentsController extends Controller
{
    public function doctors(Request $request): JsonResponse
    {
        try {
            $search = trim($request->get('search', ''));
            
            $query = User::where('role', 'doctor')
                ->where('is_active', 1)
                ->select('id', 'full_name', 'email');

            if (!empty($search)) {
                $normalized = preg_replace('/\s+/', ' ', $search);
                $wildcard = '%' . str_replace(' ', '%', $normalized) . '%';

                $query->where(function ($q) use ($wildcard) {
                    $q->whereRaw('LOWER(full_name) LIKE LOWER(?)', [$wildcard])
                        ->orWhereRaw('LOWER(email) LIKE LOWER(?)', [$wildcard]);
                });
            }

            $doctors = $query->orderBy('full_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ['doctors' => $doctors]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $start = $request->get('start', date('Y-m-d'));
            $end = $request->get('end', $start);
            $patientId = $request->get('patient_id');

            $query = Appointment::whereBetween('appointment_date', [$start, $end])
                ->with(['patient', 'doctor'])
                ->select('*');
            
            if ($patientId) {
                $query->where('patient_id', $patientId);
            }

            // Filter by user role if authenticated
            if (session('user_id')) {
                $user = User::find(session('user_id'));
                if ($user) {
                    if ($user->role === 'doctor') {
                        $query->where('doctor_id', $user->id);
                    }
                    // Secretary can see all appointments
                }
            }

            $appointments = $query->orderBy('appointment_date')->get()->map(function ($apt) {
                return [
                    ...$apt->toArray(),
                    'start_at' => $apt->appointment_date->toIso8601String() . 'Z',
                    'end_at' => $apt->appointment_date->addMinutes($apt->duration ?? 0)->toIso8601String() . 'Z',
                    'type' => $apt->appointment_type,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => ['appointments' => $appointments]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'doctor_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date_format:Y-m-d H:i',
                'appointment_type' => 'required|string|max:100',
                'chief_complaint' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:2000',
                'duration' => 'nullable|integer',
                'status' => 'nullable|in:pending,Pending,Accepted,Completed,Cancelled,No-Show',
            ], [
                'patient_id.required' => 'Patient is required',
                'doctor_id.required' => 'Please select a doctor',
                'appointment_date.required' => 'Appointment date is required',
                'appointment_type.required' => 'Appointment type is required',
                'chief_complaint.required' => 'Chief complaint is required',
            ]);

            // Format appointment_date to Y-m-d H:i:s
            $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['appointment_date']);
            $validated['appointment_date'] = $appointmentDateTime->format('Y-m-d H:i:s');
            
            // Set default status
            if (!isset($validated['status'])) {
                $validated['status'] = 'pending';
            }

            $appointment = Appointment::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created',
                'data' => $appointment
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
            $appointment = Appointment::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Get authenticated user from session
            $userId = session('user_id');
            
            // If no user in session, return unauthorized
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized - No user session found'
                ], 401);
            }
            
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized - User not found'
                ], 401);
            }
            
            // Authorization check
            // Admin and Secretary can manage any appointment
            // Doctor can only manage their own appointments
            $isAuthorized = false;
            
            if ($user->role === 'admin' || $user->role === 'secretary') {
                $isAuthorized = true;
            } elseif ($user->role === 'doctor' && $appointment->doctor_id === $user->id) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'error' => 'You do not have permission to edit this appointment'
                ], 403);
            }
            
            // Validate input
            $validated = $request->validate([
                'appointment_date' => 'nullable|date_format:Y-m-d H:i',
                'appointment_type' => 'nullable|string|max:100',
                'chief_complaint' => 'nullable|string|max:1000',
                'duration' => 'nullable|integer',
                'status' => 'nullable|in:Pending,Accepted,Completed,Cancelled,No-Show',
                'notes' => 'nullable|string|max:2000',
            ]);
            
            $oldStatus = $appointment->status;
            
            // Format appointment_date if provided
            if (isset($validated['appointment_date'])) {
                $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['appointment_date']);
                $validated['appointment_date'] = $appointmentDateTime->format('Y-m-d H:i:s');
            }
            
            // Update appointment with only validated fields
            $appointment->update($validated);

            // Log activity if status changed from pending to accepted
            if (isset($validated['status']) && 
                strtolower($oldStatus) === 'pending' && 
                strtolower($validated['status']) === 'accepted') {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'appointment_accepted',
                    'entity_type' => 'appointment',
                    'entity_id' => $appointment->id,
                    'description' => "Appointment #{$appointment->id} accepted for patient #{$appointment->patient_id}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated',
                'data' => $appointment
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            // Get authenticated user from session
            $userId = session('user_id');
            
            // If no user in session, return unauthorized
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized - No user session found'
                ], 401);
            }
            
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized - User not found'
                ], 401);
            }
            
            // Authorization check
            // Admin and Secretary can manage any appointment
            // Doctor can only manage their own appointments
            $isAuthorized = false;
            
            if ($user->role === 'admin' || $user->role === 'secretary') {
                $isAuthorized = true;
            } elseif ($user->role === 'doctor' && $appointment->doctor_id === $user->id) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'error' => 'You do not have permission to delete this appointment'
                ], 403);
            }
            
            // Log activity before deletion
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'appointment_deleted',
                'entity_type' => 'appointment',
                'entity_id' => $appointment->id,
                'description' => "Appointment #{$appointment->id} for patient #{$appointment->patient_id} deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent')
            ]);
            
            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
