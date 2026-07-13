<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['services.openai.key' => 'test-key']);

        $org = \App\Models\Organisation::factory()->create();
        $this->user = User::factory()->create(['role' => 'coordinator', 'org_id' => $org->id]);
        $this->project = Project::factory()->create(['org_id' => $org->id]);
    }

    public function test_it_can_predict_risks()
    {
        $response = $this->actingAs($this->user)->postJson("/api/v1/projects/{$this->project->id}/ai/predict-risks");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'risk_score',
                'risk_level',
                'risk_factors',
                'predicted_at',
            ]
        ]);
    }

    public function test_it_can_forecast_budget()
    {
        $response = $this->actingAs($this->user)->postJson("/api/v1/projects/{$this->project->id}/ai/budget-forecast", [
            'horizon_months' => 6
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'current_spent',
                'budget_allocated',
                'projected_spend',
                'will_over_budget',
            ]
        ]);
    }

    public function test_it_can_chat_with_ai()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => ['content' => 'Voici la réponse de l\'IA.']
                    ]
                ],
                'usage' => ['total_tokens' => 50]
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/ai/chat", [
            'message' => 'Comment améliorer ce projet ?',
            'project_id' => $this->project->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.response', 'Voici la réponse de l\'IA.');
    }
}
