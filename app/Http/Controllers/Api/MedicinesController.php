<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicinesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $search = trim($request->get('search', ''));

            $query = Medicine::where('is_active', true);

            if (!empty($search)) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('dosage', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $medicines = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => [
                    'medicines' => $medicines->items(),
                    'total' => $total,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'dosage' => 'nullable|string',
                'unit' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $medicine = Medicine::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Medicine created',
                'data' => $medicine
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $medicine = Medicine::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $medicine
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Medicine not found'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $medicine = Medicine::findOrFail($id);
            $medicine->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Medicine updated',
                'data' => $medicine
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
