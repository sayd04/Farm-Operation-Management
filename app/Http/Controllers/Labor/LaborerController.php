<?php

namespace App\Http\Controllers\Labor;

use App\Http\Controllers\Controller;
use App\Models\Laborer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LaborerController extends Controller
{
    /**
     * Display a listing of laborers
     */
    public function index(): JsonResponse
    {
        $laborers = Laborer::with(['tasks' => function($query) {
            $query->where('status', '!=', 'completed')->limit(5);
        }])->get();

        return response()->json([
            'laborers' => $laborers
        ]);
    }

    /**
     * Store a newly created laborer
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $laborer = Laborer::create($request->all());

        return response()->json([
            'message' => 'Laborer created successfully',
            'laborer' => $laborer
        ], 201);
    }

    /**
     * Display the specified laborer
     */
    public function show(Laborer $laborer): JsonResponse
    {
        $laborer->load(['tasks.planting.field', 'laborWages' => function($query) {
            $query->orderBy('date', 'desc')->limit(10);
        }]);

        return response()->json([
            'laborer' => $laborer
        ]);
    }

    /**
     * Update the specified laborer
     */
    public function update(Request $request, Laborer $laborer): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'hourly_rate' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $laborer->update($request->only(['name', 'contact', 'hourly_rate']));

        return response()->json([
            'message' => 'Laborer updated successfully',
            'laborer' => $laborer
        ]);
    }

    /**
     * Remove the specified laborer
     */
    public function destroy(Laborer $laborer): JsonResponse
    {
        // Check if laborer has active tasks
        $activeTasks = $laborer->tasks()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($activeTasks > 0) {
            return response()->json([
                'message' => 'Cannot delete laborer with active tasks'
            ], 400);
        }

        $laborer->delete();

        return response()->json([
            'message' => 'Laborer deleted successfully'
        ]);
    }
}