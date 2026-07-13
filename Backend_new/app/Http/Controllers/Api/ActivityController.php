<?php

namespace App\Http\Controllers\Api;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends \App\Http\Controllers\Controller
{
    /**
     * Get all activities for a project
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $activities = $project->activities()
            ->with('responsible')
            ->orderBy('start_date')
            ->get()
            ->map(fn($activity) => [
                'id' => $activity->id,
                'name' => $activity->name,
                'description' => $activity->description,
                'status' => $activity->status,
                'progress' => $activity->progress,
                'start_date' => $activity->start_date,
                'end_date' => $activity->end_date,
                'responsible' => $activity->responsible,
                'depends_on' => $activity->depends_on,
                'is_blocked' => $activity->isBlocked(),
                'created_at' => $activity->created_at,
            ]);

        return response()->json([
            'data' => $activities,
            'summary' => [
                'total' => $project->activities()->count(),
                'completed' => $project->activities()->where('status', 'completed')->count(),
                'in_progress' => $project->activities()->where('status', 'in_progress')->count(),
                'blocked' => $project->activities()->where('status', 'blocked')->count(),
            ],
        ]);
    }

    /**
     * Create an activity
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'depends_on' => 'nullable|exists:activities,id',
        ]);

        $activity = $project->activities()->create($validated);

        return response()->json([
            'message' => 'Activity created successfully',
            'data' => $activity,
        ], 201);
    }

    /**
     * Update an activity
     */
    public function update(Request $request, Project $project, Activity $activity): JsonResponse
    {
        $this->authorizeProject($project);
        if ($activity->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
            'status' => 'in:pending,in_progress,blocked,completed,cancelled',
            'progress' => 'integer|between:0,100',
            'end_date' => 'date|after:start_date',
        ]);

        $activity->update($validated);

        return response()->json([
            'message' => 'Activity updated successfully',
            'data' => $activity,
        ]);
    }

    /**
     * Mark activity as completed
     */
    public function complete(Request $request, Project $project, Activity $activity): JsonResponse
    {
        $this->authorizeProject($project);
        if ($activity->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'completion_note' => 'nullable|string',
        ]);

        $activity->markComplete();

        return response()->json([
            'message' => 'Activity marked as completed',
            'data' => $activity,
        ]);
    }

    /**
     * Block an activity
     */
    public function block(Request $request, Project $project, Activity $activity): JsonResponse
    {
        $this->authorizeProject($project);
        if ($activity->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $activity->update([
            'status' => 'blocked',
        ]);

        // TODO: Send notification to responsible person

        return response()->json([
            'message' => 'Activity blocked',
            'data' => $activity,
        ]);
    }

    /**
     * Get activity dependencies
     */
    public function dependencies(Project $project, Activity $activity): JsonResponse
    {
        $this->authorizeProject($project);
        if ($activity->project_id !== $project->id) abort(404);

        $dependsOn = $activity->depends_on 
            ? $project->activities()->find($activity->depends_on) 
            : null;

        $dependents = $project->activities()
            ->where('depends_on', $activity->id)
            ->get();

        return response()->json([
            'activity_id' => $activity->id,
            'depends_on' => $dependsOn,
            'dependents' => $dependents,
        ]);
    }

    /**
     * Delete an activity
     */
    public function destroy(Project $project, Activity $activity): JsonResponse
    {
        $this->authorizeProject($project);
        if ($activity->project_id !== $project->id) abort(404);

        $activity->delete();

        return response()->json(['message' => 'Activity deleted']);
    }

    /**
     * Authorize project access
     */
    private function authorizeProject(Project $project): void
    {
        if ($project->org_id !== auth()->user()->org_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access');
        }
    }
}
