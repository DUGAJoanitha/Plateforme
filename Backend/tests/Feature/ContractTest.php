<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_contract()
    {
        $org = Organisation::factory()->create();
        $user = User::factory()->create(['org_id' => $org->id, 'role' => 'coordinator']);
        Project::factory()->count(3)->create(['org_id' => $org->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/projects');
        $response->assertStatus(200);

        // Strict JSON structure contract validation
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'budget_total',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }
}
