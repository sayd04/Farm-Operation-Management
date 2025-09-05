<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\Planting;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PlantingController extends Controller
{
    /**
     * Display a listing of plantings
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Planting::query();
        
        if (!$user->isAdmin()) {
            $query->whereHas('field', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $plantings = $query->with(['field', 'crop'])->get();
        
        return response()->json([
            'plantings' => $plantings
        ]);
    }

    /**
     * Store a newly created planting
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'field_id' => 'required|exists:fields,id',
            'crop_name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after:planting_date',
            'seed_quantity' => 'required|numeric|min:0',
            'seed_unit' => 'required|string|in:kg,grams,pounds,seeds',
            'spacing' => 'nullable|array',
            'spacing.row' => 'nullable|numeric|min:0',
            'spacing.plant' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user owns the field
        $field = Field::findOrFail($request->field_id);
        $user = $request->user();
        
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to field'
            ], 403);
        }

        $planting = Planting::create([
            'field_id' => $request->field_id,
            'crop_name' => $request->crop_name,
            'variety' => $request->variety,
            'planting_date' => $request->planting_date,
            'expected_harvest_date' => $request->expected_harvest_date,
            'seed_quantity' => $request->seed_quantity,
            'seed_unit' => $request->seed_unit,
            'spacing' => $request->spacing,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Planting created successfully',
            'planting' => $planting->load(['field', 'crop'])
        ], 201);
    }

    /**
     * Display the specified planting
     */
    public function show(Request $request, Planting $planting): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $planting->field->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $planting->load(['field', 'crop', 'harvests']);

        return response()->json([
            'planting' => $planting
        ]);
    }

    /**
     * Update the specified planting
     */
    public function update(Request $request, Planting $planting): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $planting->field->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'crop_name' => 'sometimes|required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'sometimes|required|date',
            'expected_harvest_date' => 'nullable|date|after:planting_date',
            'seed_quantity' => 'sometimes|required|numeric|min:0',
            'seed_unit' => 'sometimes|required|string|in:kg,grams,pounds,seeds',
            'spacing' => 'nullable|array',
            'spacing.row' => 'nullable|numeric|min:0',
            'spacing.plant' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $planting->update($request->only([
            'crop_name', 'variety', 'planting_date', 'expected_harvest_date',
            'seed_quantity', 'seed_unit', 'spacing', 'notes'
        ]));

        return response()->json([
            'message' => 'Planting updated successfully',
            'planting' => $planting->load(['field', 'crop'])
        ]);
    }

    /**
     * Remove the specified planting
     */
    public function destroy(Request $request, Planting $planting): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $planting->field->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $planting->delete();

        return response()->json([
            'message' => 'Planting deleted successfully'
        ]);
    }
}