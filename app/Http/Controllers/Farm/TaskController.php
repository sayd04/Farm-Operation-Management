<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Planting;
use App\Models\Laborer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Task::with(['planting.field', 'laborer']);
        
        if (!$user->isAdmin()) {
            // Only show tasks from user's plantings
            $plantingIds = Planting::whereHas('field', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->pluck('_id');
            
            $query->whereIn('planting_id', $plantingIds);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by overdue
        if ($request->get('overdue') === 'true') {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', Task::STATUS_COMPLETED);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'planting_id' => 'required|string',
            'task_type' => 'required|in:watering,fertilizing,weeding,pest_control,harvesting,maintenance',
            'due_date' => 'required|date',
            'description' => 'required|string',
            'assigned_to' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify planting ownership
        $planting = Planting::find($request->planting_id);
        if (!$planting) {
            return response()->json(['message' => 'Planting not found'], 404);
        }

        if (!$request->user()->isAdmin() && $planting->field->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Verify laborer exists if assigned
        if ($request->assigned_to) {
            $laborer = Laborer::find($request->assigned_to);
            if (!$laborer) {
                return response()->json(['message' => 'Laborer not found'], 404);
            }
        }

        $task = Task::create([
            'planting_id' => $request->planting_id,
            'task_type' => $request->task_type,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $request->assigned_to,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load(['planting.field', 'laborer'])
        ], 201);
    }

    /**
     * Display the specified task
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $task->planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->load(['planting.field', 'laborer', 'laborWages']);

        return response()->json([
            'task' => $task
        ]);
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $task->planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'task_type' => 'sometimes|in:watering,fertilizing,weeding,pest_control,harvesting,maintenance',
            'due_date' => 'sometimes|date',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify laborer exists if assigned
        if ($request->assigned_to) {
            $laborer = Laborer::find($request->assigned_to);
            if (!$laborer) {
                return response()->json(['message' => 'Laborer not found'], 404);
            }
        }

        $task->update($request->only([
            'task_type', 'due_date', 'description', 'status', 'assigned_to'
        ]));

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task->load(['planting.field', 'laborer'])
        ]);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $task->planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->markCompleted();

        return response()->json([
            'message' => 'Task marked as completed',
            'task' => $task->load(['planting.field', 'laborer'])
        ]);
    }

    /**
     * Remove the specified task
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $task->planting->field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}