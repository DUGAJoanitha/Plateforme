<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AIRecommendation;
use App\Models\Project;
use App\Models\Report;
use App\Services\AIService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectStatusExport;

class ReportController extends Controller
{
    public function __construct(protected AIService $aiService) {}

    /**
     * GET /api/v1/projects/{project}/reports
     * Lister les rapports d'un projet.
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $reports = Report::where('project_id', $project->id)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json(['data' => $reports]);
    }

    /**
     * POST /api/v1/projects/{project}/reports/generate
     * Génère un rapport PDF du projet.
     */
    public function generate(Request $request, Project $project): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'type'   => 'required|in:summary,financial,activities,full',
            'format' => 'nullable|in:pdf,excel',
            'title'  => 'nullable|string|max:255',
        ]);

        $format = $validated['format'] ?? 'pdf';

        // Charger toutes les données du projet
        $project->load(['organisation', 'activities', 'kpis', 'budgetLines.expenses', 'risks']);

        // Calcul des statistiques
        $totalActivities  = $project->activities->count();
        $completedActs    = $project->activities->where('status', 'completed')->count();
        $delayedActs      = $project->activities->filter(fn($a) => $a->isDelayed())->count();
        $kpiTotal         = $project->kpis->count();
        $kpiOnTrack       = $project->kpis->filter(fn($k) => $k->isOnTrack())->count();
        $budgetAlloc      = $project->budgetLines->sum('allocated');
        $budgetSpent      = $project->budgetLines->sum('spent');
        $budgetPct        = $budgetAlloc > 0 ? round(($budgetSpent / $budgetAlloc) * 100) : 0;
        $progressPct      = $totalActivities > 0 ? round(($completedActs / $totalActivities) * 100) : 0;

        $stats = [
            'progress_pct'       => $progressPct,
            'budget_pct'         => $budgetPct,
            'kpi_on_track'       => $kpiOnTrack,
            'kpi_total'          => $kpiTotal,
            'delayed_activities' => $delayedActs,
        ];

        // Recommandations IA
        $aiRecommendations = AIRecommendation::where('project_id', $project->id)
            ->whereNull('read_at')
            ->latest('generated_at')
            ->take(5)
            ->get();

        // Si pas de recommandations IA enregistrées, en générer de nouvelles
        if ($aiRecommendations->isEmpty()) {
            $analysis = $this->aiService->analyzeProject($project);
            $aiRecommendations = collect($analysis['recommendations'] ?? [])->map(function ($rec) use ($project) {
                return AIRecommendation::create([
                    'project_id'   => $project->id,
                    'type'         => $rec['type'],
                    'content'      => json_encode($rec),
                    'confidence'   => 0.75,
                    'generated_at' => now(),
                ]);
            });
        }

        // Créer l'entrée Report en base
        $reportTitle = $validated['title'] ?? "Rapport {$validated['type']} — {$project->name} — " . now()->format('d/m/Y');
        $report = Report::create([
            'project_id' => $project->id,
            'user_id'    => $request->user()->id,
            'title'      => $reportTitle,
            'type'       => $validated['type'],
            'status'     => 'pending',
            'parameters' => ['type' => $validated['type']],
        ]);

        // Sélection des données selon le type
        $viewData = [
            'report'             => $report,
            'project'            => $project,
            'stats'              => $stats,
            'aiRecommendations'  => $aiRecommendations,
            'activities'         => in_array($validated['type'], ['activities', 'full']) ? $project->activities : collect(),
            'kpis'               => in_array($validated['type'], ['summary', 'full']) ? $project->kpis : collect(),
            'budgetLines'        => in_array($validated['type'], ['financial', 'full']) ? $project->budgetLines : collect(),
        ];

        // Génération du Fichier
        try {
            $filename = 'reports/' . Str::slug($reportTitle) . '-' . $report->id;

            if ($format === 'excel') {
                $filename .= '.xlsx';
                Excel::store(new ProjectStatusExport(collect([$project])), $filename, 'public');
            } else {
                $filename .= '.pdf';
                $pdf = Pdf::loadView('reports.project', $viewData)
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);
                
                Storage::disk('public')->put($filename, $pdf->output());
            }

            $report->update([
                'status'     => 'generated',
                'file_path'  => $filename,
                'parameters' => array_merge($report->parameters ?? [], ['format' => $format]),
            ]);
        } catch (\Exception $e) {
            $report->update(['status' => 'failed']);
            return response()->json(['message' => 'Erreur lors de la génération du rapport.', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message'     => 'Rapport généré avec succès',
            'data'        => $report->fresh(),
            'download_url' => url("/api/v1/reports/{$report->id}/download"),
        ], 201);
    }

    /**
     * GET /api/v1/reports/{report}/download
     * Télécharger un rapport PDF.
     */
    public function download(Report $report): Response
    {
        $this->authorizeProject($report->project);

        if ($report->status !== 'generated' || !$report->file_path) {
            abort(404, 'Rapport non disponible');
        }

        if (!Storage::disk('public')->exists($report->file_path)) {
            abort(404, 'Fichier introuvable');
        }

        $contents = Storage::disk('public')->get($report->file_path);
        $filename = basename($report->file_path);

        $contentType = Str::endsWith($filename, '.xlsx') 
            ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            : 'application/pdf';

        return response($contents, 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * DELETE /api/v1/reports/{report}
     * Supprimer un rapport.
     */
    public function destroy(Report $report): JsonResponse
    {
        $this->authorizeProject($report->project);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

        return response()->json(['message' => 'Rapport supprimé']);
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function authorizeProject(Project $project): void
    {
        if ($project->org_id !== auth()->user()->org_id && auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access to this project');
        }
    }
}
