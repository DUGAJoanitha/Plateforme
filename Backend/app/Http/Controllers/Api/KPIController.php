<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\KPIResource;
use App\Models\KPI;
use App\Models\KPIMeasure;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KPIController extends \App\Http\Controllers\Controller
{
    /**
     * Get all KPIs for a project
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $kpis = $project->kpis()
            ->with('measures')
            ->get();

        return response()->json([
            'data' => KPIResource::collection($kpis),
        ]);
    }

    /**
     * Create a new KPI
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_value' => 'required|numeric|min:0',
            'baseline' => 'nullable|numeric',
            'unit' => 'required|string|max:50',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
        ]);

        $kpi = $project->kpis()->create($validated);

        return response()->json([
            'message' => 'KPI created successfully',
            'data' => new KPIResource($kpi),
        ], 201);
    }

    /**
     * Update a KPI
     */
    public function update(Request $request, Project $project, KPI $kpi): JsonResponse
    {
        $this->authorizeProject($project);
        if ($kpi->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'target_value' => 'numeric|min:0',
            'baseline' => 'nullable|numeric',
            'unit' => 'string|max:50',
            'frequency' => 'in:daily,weekly,monthly,quarterly,yearly',
        ]);

        $kpi->update($validated);

        return response()->json([
            'message' => 'KPI updated successfully',
            'data' => new KPIResource($kpi),
        ]);
    }

    /**
     * Add a measurement to a KPI
     */
    public function addMeasure(Request $request, Project $project, KPI $kpi): JsonResponse
    {
        $this->authorizeProject($project);
        if ($kpi->project_id !== $project->id) abort(404);

        $validated = $request->validate([
            'value' => 'required|numeric',
            'collected_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $measure = $kpi->measures()->create([
            ...$validated,
            'collected_by' => $request->user()->id,
        ]);

        // Update KPI current value with latest measure
        $kpi->update(['current_value' => $measure->value]);

        return response()->json([
            'message' => 'Measurement added successfully',
            'data' => [
                'measure' => $measure,
                'kpi_performance' => $kpi->calculatePerformance(),
            ],
        ], 201);
    }

    /**
     * Get KPI measurement history
     */
    public function history(Project $project, KPI $kpi, Request $request): JsonResponse
    {
        $this->authorizeProject($project);
        if ($kpi->project_id !== $project->id) abort(404);

        $query = $kpi->measures();

        // Date range filter
        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('collected_at', [
                $request->from,
                $request->to,
            ]);
        }

        $measures = $query->orderBy('collected_at', 'desc')->paginate(50);

        return response()->json([
            'data' => $measures,
            'meta' => [
                'total' => $measures->total(),
                'per_page' => $measures->perPage(),
            ],
        ]);
    }

    /**
     * Get KPI performance analysis
     */
    public function performance(Project $project, KPI $kpi): JsonResponse
    {
        $this->authorizeProject($project);
        if ($kpi->project_id !== $project->id) abort(404);

        $measures = $kpi->measures()->orderBy('collected_at')->get();
        $performance = $kpi->calculatePerformance();
        $isOnTrack = $kpi->isOnTrack();

        return response()->json([
            'kpi' => new KPIResource($kpi),
            'analysis' => [
                'performance_percentage' => $performance,
                'is_on_track' => $isOnTrack,
                'status' => $isOnTrack ? 'ON TRACK' : 'AT RISK',
                'measures_count' => $measures->count(),
                'trend' => $this->calculateTrend($measures),
            ],
        ]);
    }

    /**
     * Calculate trend (improving, stable, declining)
     */
    private function calculateTrend($measures): string
    {
        if ($measures->count() < 2) return 'insufficient_data';

        $recent = $measures->slice(-5)->pluck('value')->toArray();
        if (count($recent) < 2) return 'insufficient_data';

        $avg_recent = array_sum(array_slice($recent, -3)) / min(3, count($recent));
        $avg_previous = array_sum(array_slice($recent, 0, -3)) / max(1, count($recent) - 3);

        if ($avg_recent > $avg_previous * 1.05) return 'improving';
        if ($avg_recent < $avg_previous * 0.95) return 'declining';
        return 'stable';
    }

    /**
     * Delete a KPI
     */
    public function destroy(Project $project, KPI $kpi): JsonResponse
    {
        $this->authorizeProject($project);
        if ($kpi->project_id !== $project->id) abort(404);

        $kpi->delete();

        return response()->json(['message' => 'KPI deleted successfully']);
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
