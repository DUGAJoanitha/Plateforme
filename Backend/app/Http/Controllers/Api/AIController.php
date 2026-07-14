<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AIRecommendation;
use App\Models\Project;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(protected AIService $aiService) {}

    /**
     * POST /api/v1/projects/{project}/ai/analyze
     * Lance une analyse complète du projet par l'IA.
     */
    public function analyze(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $result = $this->aiService->analyzeProject($project);

        // Persister les recommandations en base
        if (!empty($result['recommendations'])) {
            foreach ($result['recommendations'] as $rec) {
                AIRecommendation::create([
                    'project_id'   => $project->id,
                    'type'         => $rec['type'],
                    'content'      => json_encode($rec),
                    'confidence'   => $rec['priority'] === 'CRITICAL' ? 0.9 : 0.7,
                    'generated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'data'    => $result,
            'message' => 'Project analysis completed',
        ]);
    }

    /**
     * POST /api/v1/projects/{project}/ai/predict-risks
     * Prédit les risques du projet.
     */
    public function predictRisks(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $result = $this->aiService->predictRisks($project);

        // Mettre à jour le risk_score du projet
        $project->update(['risk_score' => $result['risk_score']]);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/v1/projects/{project}/ai/budget-forecast
     * Prévision budgétaire sur N mois.
     */
    public function budgetForecast(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'horizon_months' => 'nullable|integer|min:1|max:24',
        ]);

        $result = $this->aiService->forecastBudget($project, $validated['horizon_months'] ?? 3);

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/v1/projects/{project}/ai/recommendations
     * Récupère les recommandations IA enregistrées pour un projet.
     */
    public function recommendations(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $recommendations = AIRecommendation::where('project_id', $project->id)
            ->orderByDesc('generated_at')
            ->paginate(10);

        return response()->json(['data' => $recommendations]);
    }

    /**
     * DELETE /api/v1/ai/recommendations/{recommendation}
     * Marque une recommandation comme lue.
     */
    public function markRead(AIRecommendation $recommendation): JsonResponse
    {
        $recommendation->markAsRead();

        return response()->json(['message' => 'Recommendation marked as read']);
    }

    /**
     * POST /api/v1/ai/chat
     * Assistant IA conversationnel.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message'    => 'required|string|max:2000',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $project = isset($validated['project_id'])
            ? Project::find($validated['project_id'])
            : null;

        if ($project) {
            $this->authorizeProject($project);
        }

        $result = $this->aiService->chat($validated['message'], $project);

        return response()->json(['data' => $result]);
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function authorizeProject(Project $project): void
    {
        if ($project->org_id !== auth()->user()->org_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access to this project');
        }
    }
}
