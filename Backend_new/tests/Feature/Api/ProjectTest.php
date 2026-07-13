<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Project;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    private User $user;
    private Organisation $organisation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::factory()->create();
        $this->user = User::factory()->create([
            'org_id' => $this->organisation->id,
            'role' => 'coordinator',
        ]);
    }

    /**
     * Test can list projects
     */
    public function test_can_list_projects(): void
    {
        Project::factory(5)->create([
            'org_id' => $this->organisation->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'status',
                        'budget_total',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data');
    }

    /**
     * Test can create project
     */
    public function test_can_create_project(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/projects', [
                'name' => 'Water Project 2025',
                'description' => 'Provide clean water',
                'budget_total' => 50000,
                'start_date' => '2025-06-01',
                'end_date' => '2026-06-01',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'status',
                    'budget_total',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Water Project 2025',
            'org_id' => $this->organisation->id,
        ]);
    }

    /**
     * Test can view project
     */
    public function test_can_view_project(): void
    {
        $project = Project::factory()->create([
            'org_id' => $this->organisation->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
            ]);
    }

    /**
     * Test can update project
     */
    public function test_can_update_project(): void
    {
        $project = Project::factory()->create([
            'org_id' => $this->organisation->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/projects/{$project->id}", [
                'name' => 'Updated Project Name',
                'budget_total' => 75000,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'budget_total' => 75000,
        ]);
    }

    /**
     * Test can delete project
     */
    public function test_can_delete_project(): void
    {
        $project = Project::factory()->create([
            'org_id' => $this->organisation->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    /**
     * Test can view project dashboard
     */
    public function test_can_view_project_dashboard(): void
    {
        $project = Project::factory()->create([
            'org_id' => $this->organisation->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/projects/{$project->id}/dashboard");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'activities',
                    'kpis',
                    'budget',
                    'risks',
                ],
            ]);
    }

    /**
     * Test unauthenticated user cannot access projects
     */
    public function test_unauthenticated_user_cannot_access_projects(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(401);
    }

    /**
     * Test user cannot access other org projects
     */
    public function test_user_cannot_access_other_org_projects(): void
    {
        $otherOrg = Organisation::factory()->create();
        $project = Project::factory()->create([
            'org_id' => $otherOrg->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(403);
    }
}
