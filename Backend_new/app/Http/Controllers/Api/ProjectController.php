<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group 📁 Projets
 *
 * Gestion des projets, dashboard, et duplication. Tous les endpoints nécessitent une authentification.
 */
class ProjectController extends \App\Http\Controllers\Controller
{
    /**
     * Get paginated list of projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()
            ->with('programme', 'organisation')
            ->where('org_id', $request->user()->org_id);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('programme_id')) {
            $query->where('programme_id', $request->programme_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $projects = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => ProjectResource::collection($projects),
            'meta' => [
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
            ],
        ]);
    }

    /**
     * Create a new project
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget_total' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'programme_id' => 'nullable|exists:programmes,id',
        ]);

        $project = Project::create([
            ...$validated,
            'org_id' => $request->user()->org_id,
            'status' => 'planning',
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ], 201);
    }

    /**
     * Get project details
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $project->load(['activities', 'kpis', 'budgetLines', 'risks']);

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Update a project
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'budget_total' => 'numeric|min:0',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:planning,active,on_hold,completed,cancelled',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Delete (archive) a project
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $project->delete();

        return response()->json([
            'message' => 'Project archived successfully',
        ]);
    }

    /**
     * Get project dashboard summary
     */
    public function dashboard(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $completedActivities = $project->activities()->where('status', 'completed')->count();
        $totalActivities = $project->activities()->count();
        $onTrackKPIs = $project->kpis()->get()->filter(fn($kpi) => $kpi->isOnTrack())->count();

        return response()->json([
            'data' => [
                'project' => new ProjectResource($project),
                'activities' => [
                    'completed' => $completedActivities,
                    'total' => $totalActivities,
                    'percentage' => $totalActivities > 0 ? ($completedActivities / $totalActivities) * 100 : 0,
                ],
                'kpis' => [
                    'on_track' => $onTrackKPIs,
                    'total' => $project->kpis()->count(),
                ],
                'budget' => [
                    'allocated' => $project->budgetLines()->sum('allocated'),
                    'spent' => $project->budgetLines()->sum('spent'),
                ],
                'risks' => [
                    'count' => $project->risks()->count(),
                    'critical' => $project->risks()->where('score', '>=', 20)->count(),
                ],
            ]
        ]);
    }

    /**
     * Get project risk score (AI-based)
     */
    public function riskScore(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        // Simple risk calculation (to be enhanced with AI)
        $risks = $project->risks()->get();
        $avgRiskScore = $risks->avg('score') ?? 0;

        return response()->json([
            'project_id' => $project->id,
            'risk_score' => $avgRiskScore,
            'risk_level' => $this->getRiskLevel($avgRiskScore),
            'risks_count' => $risks->count(),
        ]);
    }

    /**
     * Duplicate a project
     */
    public function duplicate(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'new_name' => 'required|string|max:255',
            'new_start_date' => 'required|date',
        ]);

        // Create new project
        $newProject = $project->replicate();
        $newProject->name = $validated['new_name'];
        $newProject->start_date = $validated['new_start_date'];
        // Calculate new end date based on duration
        $duration = $project->end_date->diffInDays($project->start_date);
        $newProject->end_date = $newProject->start_date->addDays($duration);
        $newProject->save();

        return response()->json([
            'message' => 'Project duplicated successfully',
            'data' => new ProjectResource($newProject),
        ], 201);
    }

    /**
     * Authorize project access
     */
    private function authorizeProject(Project $project): void
    {
        if ($project->org_id !== auth()->user()->org_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access to this project');
        }
    }

    /**
     * Get risk level label
     */
    private function getRiskLevel($score): string
    {
        if ($score >= 75) return 'CRITICAL';
        if ($score >= 50) return 'HIGH';
        if ($score >= 25) return 'MEDIUM';
        return 'LOW';
    }
}
