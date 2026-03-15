<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->get('limit', 10);
            $search = trim($request->get('search', ''));

            $query = Patient::query();

            // Apply search filter only if search term is provided and non-empty
            if (!empty($search) && $search !== 'undefined') {
                $normalized = preg_replace('/\s+/', ' ', $search);
                $wildcard = '%' . str_replace(' ', '%', $normalized) . '%';

                $query->where(function ($q) use ($wildcard, $search) {
                    $q->whereRaw('LOWER(CONCAT_WS(\' \', first_name, last_name)) LIKE LOWER(?)', [$wildcard])
                        ->orWhereRaw('LOWER(CONCAT_WS(\' \', last_name, first_name)) LIKE LOWER(?)', [$wildcard])
                        ->orWhereRaw('LOWER(first_name) LIKE LOWER(?)', [$wildcard])
                        ->orWhereRaw('LOWER(last_name) LIKE LOWER(?)', [$wildcard])
                        ->orWhereRaw('LOWER(email) LIKE LOWER(?)', [$wildcard])
                        ->orWhere('phone', 'like', $search);
                });
            }

            // Get paginated results - let Laravel handle page from query string
            $patients = $query->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'patients' => $patients->items(),
                    'total' => $patients->total(),
                    'page' => $patients->currentPage(),
                    'limit' => $limit,
                    'pages' => $patients->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Patient API Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|in:Male,Female,Other',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'email' => 'nullable|email',
                'phone' => 'required|string',
                'occupation' => 'nullable|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                'postal_code' => 'nullable|string',
                'height' => 'required|numeric|min:0',
                'weight' => 'required|numeric|min:0',
                'allergies' => 'nullable|string',
                'vaccine_history' => 'nullable|string',
                'emergency_contact_name' => 'required|string',
                'emergency_contact_relationship' => 'required|string',
                'emergency_contact_phone' => 'required|string',
            ]);

            // Generate unique patient_id
            $validated['patient_id'] = 'PAT-' . time() . '-' . rand(1000, 9999);

            // Calculate BMI
            $validated['bmi'] = $validated['height'] > 0 
                ? round($validated['weight'] / (($validated['height'] / 100) ** 2), 2)
                : null;

            $patient = Patient::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Patient created',
                'data' => $patient
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
            ], 400);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $patient
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Patient not found'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'gender' => 'required|in:Male,Female,Other',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'email' => 'required|email|max:100|unique:patients,email,' . $id,
                'phone' => 'required|string|max:20',
                'occupation' => 'nullable|string|max:100',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'height' => 'required|numeric|min:0',
                'weight' => 'required|numeric|min:0',
                'allergies' => 'nullable|string',
                'vaccine_history' => 'nullable|string',
                'emergency_contact_name' => 'required|string|max:100',
                'emergency_contact_relationship' => 'required|string|max:50',
                'emergency_contact_phone' => 'required|string|max:20',
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'gender.required' => 'Gender is required',
                'date_of_birth.required' => 'Date of birth is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email is already in use',
                'phone.required' => 'Phone number is required',
                'address.required' => 'Address is required',
                'height.required' => 'Height is required',
                'weight.required' => 'Weight is required',
            ]);

            // Normalize gender casing and calculate BMI
            if (isset($validated['gender'])) {
                $validated['gender'] = ucfirst(strtolower($validated['gender']));
            }

            $validated['bmi'] = $validated['height'] > 0 
                ? round($validated['weight'] / (($validated['height'] / 100) ** 2), 2)
                : null;

            $patient->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Patient updated',
                'data' => $patient
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
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();

            return response()->json([
                'success' => true,
                'message' => 'Patient deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
