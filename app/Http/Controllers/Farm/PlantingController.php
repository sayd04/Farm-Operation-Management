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
        
        $query = Planting::query()->with(['field', 'harvests', 'tasks', 'expenses']);
        
        if (!$user->isAdmin()) {
            // Only show plantings from user's fields
            $fieldIds = Field::where('user_id', $user->id)->pluck('_id');
            $query->whereIn('field_id', $fieldIds);
        }
        
        $plantings = $query->orderBy('created_at', 'desc')->get();
        
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
            'field_id' => 'required|string',
            'crop_type' => 'required|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'required|date|after:planting_date',
            'status' => 'sometimes|in:planted,growing,ready,harvested,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify field ownership
        $field = Field::find($request->field_id);
        if (!$field) {
            return response()->json(['message' => 'Field not found'], 404);
        }

        if (!$request->user()->isAdmin() && $field->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $planting = Planting::create([
            'field_id' => $request->field_id,
            'crop_type' => $request->crop_type,
            'planting_date' => $request->planting_date,
            'expected_harvest_date' => $request->expected_harvest_date,
            'status' => $request->status ?? Planting::STATUS_PLANTED,
        ]);

        return response()->json([
            'message' => 'Planting created successfully',
            'planting' => $planting->load(['field', 'harvests', 'tasks', 'expenses'])
        ], 201);
    }

    /**
     * Display the specified planting
     */
    public function show(Request $request, Planting $planting): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $planting->load(['field', 'harvests', 'tasks.laborer', 'expenses']);

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
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'crop_type' => 'sometimes|string|max:255',
            'planting_date' => 'sometimes|date',
            'expected_harvest_date' => 'sometimes|date|after:planting_date',
            'status' => 'sometimes|in:planted,growing,ready,harvested,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $planting->update($request->only([
            'crop_type', 'planting_date', 'expected_harvest_date', 'status'
        ]));

        return response()->json([
            'message' => 'Planting updated successfully',
            'planting' => $planting->load(['field', 'harvests', 'tasks', 'expenses'])
        ]);
    }

    /**
     * Remove the specified planting
     */
    public function destroy(Request $request, Planting $planting): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if planting has harvests
        if ($planting->harvests()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete planting with existing harvests'
            ], 400);
        }

        $planting->delete();

        return response()->json([
            'message' => 'Planting deleted successfully'
        ]);
    }
}