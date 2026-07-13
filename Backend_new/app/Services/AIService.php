<?php

namespace App\Services;

use App\Models\Project;
use App\Models\AIRecommendation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected string $openaiKey;
    protected bool $useMock;

    public function __construct()
    {
        $this->openaiKey = config('services.openai.key', '');
        $this->useMock = empty($this->openaiKey);
    }

    /**
     * Analyse complète d'un projet et génère des recommandations.
     */
    public function analyzeProject(Project $project): array
    {
        $project->load(['activities', 'kpis', 'budgetLines', 'risks']);

        $analysis = $this->buildProjectAnalysis($project);

        if ($this->useMock) {
            return $this->mockAnalysis($project, $analysis);
        }

        return $this->callOpenAI($analysis, 'analysis');
    }

    /**
     * Prédiction du score de risque basée sur les données du projet.
     */
    public function predictRisks(Project $project): array
    {
        $project->load(['activities', 'budgetLines', 'risks']);

        // Calcul algorithme de risque (règles métier)
        $score = $this->calculateRiskScore($project);
        $factors = $this->identifyRiskFactors($project);

        return [
            'risk_score'   => round($score, 1),
            'risk_level'   => $this->getRiskLevel($score),
            'risk_factors' => $factors,
            'predicted_at' => now()->toISOString(),
        ];
    }

    /**
     * Prévision budgétaire basée sur la tendance actuelle.
     */
    public function forecastBudget(Project $project, int $horizonMonths = 3): array
    {
        $project->load('budgetLines.expenses');

        $allocated  = $project->budgetLines->sum('allocated');
        $spent      = $project->budgetLines->sum('spent');
        $remaining  = $allocated - $spent;

        // Calcul du taux de consommation mensuel
        $startDate     = $project->start_date;
        $monthsElapsed = max(1, $startDate->diffInMonths(now()));
        $burnRate      = $spent / $monthsElapsed;

        $projectedSpend  = $spent + ($burnRate * $horizonMonths);
        $willOverBudget  = $projectedSpend > $allocated;
        $monthsUntilOut  = $burnRate > 0 ? $remaining / $burnRate : null;

        return [
            'current_spent'          => round($spent, 2),
            'budget_allocated'       => round($allocated, 2),
            'burn_rate_monthly'      => round($burnRate, 2),
            'projected_spend'        => round($projectedSpend, 2),
            'will_over_budget'       => $willOverBudget,
            'months_until_depleted'  => $monthsUntilOut ? round($monthsUntilOut, 1) : null,
            'horizon_months'         => $horizonMonths,
        ];
    }

    /**
     * Chat conversationnel avec l'IA sur le contexte d'un projet.
     */
    public function chat(string $message, ?Project $project = null): array
    {
        if ($this->useMock) {
            return $this->mockChat($message, $project);
        }

        $context = $project
            ? "Tu es un assistant expert en suivi-évaluation de projets. Le projet actuel est '{$project->name}' avec un budget de {$project->budget_total}. "
            : "Tu es un assistant expert en suivi-évaluation de projets et programmes pour ONG. ";

        $response = Http::withHeaders(['Authorization' => "Bearer {$this->openaiKey}"])
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => $message],
                ],
                'max_tokens' => 500,
            ]);

        if ($response->failed()) {
            return ['response' => 'Le service IA est temporairement indisponible.', 'error' => true];
        }

        return [
            'response'    => $response->json('choices.0.message.content'),
            'model'       => 'gpt-4o-mini',
            'tokens_used' => $response->json('usage.total_tokens'),
        ];
    }

    /**
     * Récupère ou génère les recommandations pour un projet.
     */
    public function getRecommendations(Project $project): array
    {
        $cacheKey = "ai_recommendations_{$project->id}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($project) {
            $analysis = $this->analyzeProject($project);
            return $analysis['recommendations'] ?? [];
        });
    }

    // ─── Méthodes privées ──────────────────────────────────────────────────

    private function buildProjectAnalysis(Project $project): array
    {
        $activities      = $project->activities;
        $kpis            = $project->kpis;
        $budgetLines     = $project->budgetLines;
        $totalActivities = $activities->count();
        $completed       = $activities->where('status', 'completed')->count();
        $delayed         = $activities->filter(fn($a) => $a->isDelayed())->count();

        $totalKpis   = $kpis->count();
        $onTrackKpis = $kpis->filter(fn($k) => $k->isOnTrack())->count();

        $allocated = $budgetLines->sum('allocated');
        $spent     = $budgetLines->sum('spent');
        $burnPct   = $allocated > 0 ? ($spent / $allocated) * 100 : 0;

        $progressPct = $totalActivities > 0 ? ($completed / $totalActivities) * 100 : 0;

        return compact(
            'totalActivities', 'completed', 'delayed',
            'totalKpis', 'onTrackKpis',
            'allocated', 'spent', 'burnPct', 'progressPct'
        );
    }

    private function calculateRiskScore(Project $project): float
    {
        $score = 0;

        // Retards sur les activités (+30)
        $delayed = $project->activities->filter(fn($a) => $a->isDelayed())->count();
        $total   = max(1, $project->activities->count());
        $score += ($delayed / $total) * 30;

        // Dépassement budgétaire (+40)
        $allocated = $project->budgetLines->sum('allocated');
        $spent     = $project->budgetLines->sum('spent');
        if ($allocated > 0) {
            $burnRate = ($spent / $allocated) * 100;
            if ($burnRate > 90) $score += 40;
            elseif ($burnRate > 75) $score += 20;
            elseif ($burnRate > 60) $score += 10;
        }

        // KPI hors trajectoire (+20)
        $kpis       = $project->kpis;
        $offTrack   = $kpis->filter(fn($k) => !$k->isOnTrack())->count();
        $totalKpis  = max(1, $kpis->count());
        $score += ($offTrack / $totalKpis) * 20;

        // Risques critiques enregistrés (+10)
        $criticalRisks = $project->risks->where('score', '>=', 75)->count();
        $score += min(10, $criticalRisks * 5);

        return min(100, $score);
    }

    private function identifyRiskFactors(Project $project): array
    {
        $factors = [];

        $delayed = $project->activities->filter(fn($a) => $a->isDelayed())->count();
        if ($delayed > 0) {
            $factors[] = ['type' => 'activity_delay', 'severity' => 'HIGH', 'description' => "{$delayed} activité(s) en retard détectée(s)."];
        }

        $allocated = $project->budgetLines->sum('allocated');
        $spent     = $project->budgetLines->sum('spent');
        if ($allocated > 0 && ($spent / $allocated) > 0.9) {
            $factors[] = ['type' => 'budget_overrun', 'severity' => 'CRITICAL', 'description' => 'Le budget dépasse 90% de consommation.'];
        }

        $offTrack = $project->kpis->filter(fn($k) => !$k->isOnTrack())->count();
        if ($offTrack > 0) {
            $factors[] = ['type' => 'kpi_off_track', 'severity' => 'MEDIUM', 'description' => "{$offTrack} KPI(s) hors trajectoire."];
        }

        return $factors;
    }

    private function mockAnalysis(Project $project, array $analysis): array
    {
        $recs = [];

        if ($analysis['delayed'] > 0) {
            $recs[] = [
                'type'        => 'activity_delay',
                'title'       => "Réduire les retards",
                'description' => "{$analysis['delayed']} activité(s) en retard. Priorisez leur déblocage en semaine.",
                'priority'    => 'HIGH',
            ];
        }

        if ($analysis['burnPct'] > 75) {
            $recs[] = [
                'type'        => 'budget_warning',
                'title'       => "Surveiller la consommation budgétaire",
                'description' => "Le budget est consommé à " . round($analysis['burnPct']) . "%. Révisez les dépenses planifiées.",
                'priority'    => 'CRITICAL',
            ];
        }

        if ($analysis['onTrackKpis'] < $analysis['totalKpis']) {
            $offTrack = $analysis['totalKpis'] - $analysis['onTrackKpis'];
            $recs[] = [
                'type'        => 'kpi_trend',
                'title'       => "KPI sous-performants",
                'description' => "{$offTrack} indicateur(s) n'atteignent pas leurs cibles. Analysez les causes racines.",
                'priority'    => 'MEDIUM',
            ];
        }

        if (empty($recs)) {
            $recs[] = [
                'type'        => 'general_insight',
                'title'       => "Projet sur la bonne voie",
                'description' => "Toutes les métriques sont dans les paramètres attendus. Continuez !",
                'priority'    => 'LOW',
            ];
        }

        return [
            'summary'         => "Le projet '{$project->name}' a un avancement de " . round($analysis['progressPct']) . "% avec un taux de consommation budgétaire de " . round($analysis['burnPct']) . "%.",
            'recommendations' => $recs,
            'generated_at'    => now()->toISOString(),
            'mode'            => 'rule-based',
        ];
    }

    private function mockChat(string $message, ?Project $project = null): array
    {
        $context = $project ? "Projet : {$project->name}. " : '';
        return [
            'response'    => "🤖 [Mode démo] {$context}Votre question : \"{$message}\". Pour activer l'IA réelle, configurez la clé OPENAI_API_KEY dans votre fichier .env.",
            'model'       => 'mock',
            'tokens_used' => 0,
        ];
    }

    private function callOpenAI(array $context, string $type): array
    {
        // Appel réel à GPT-4o-mini — à utiliser si OPENAI_API_KEY est configuré
        $prompt = "Analyse ce projet de développement et fournis des recommandations : " . json_encode($context);

        $response = Http::withHeaders(['Authorization' => "Bearer {$this->openaiKey}"])
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un expert en suivi-évaluation de projets de développement.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 800,
            ]);

        if ($response->failed()) {
            return $this->mockAnalysis(new Project(), $context);
        }

        return ['summary' => $response->json('choices.0.message.content'), 'generated_at' => now()->toISOString(), 'mode' => 'openai'];
    }

    private function getRiskLevel(float $score): string
    {
        if ($score >= 75) return 'CRITICAL';
        if ($score >= 50) return 'HIGH';
        if ($score >= 25) return 'MEDIUM';
        return 'LOW';
    }
}
