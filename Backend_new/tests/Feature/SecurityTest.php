<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_protected_api()
    {
        $response = $this->getJson('/api/v1/auth/me');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_protected_api()
    {
        $org = Organisation::factory()->create();
        $user = User::factory()->create(['org_id' => $org->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/auth/me');
        $response->assertStatus(200);
    }

    public function test_user_cannot_access_project_from_another_organisation()
    {
        $org1 = Organisation::factory()->create();
        $org2 = Organisation::factory()->create();

        $user = User::factory()->create(['org_id' => $org1->id, 'role' => 'coordinator']);
        $project = Project::factory()->create(['org_id' => $org2->id]);

        // Route to get project details
        $response = $this->actingAs($user)->getJson("/api/v1/projects/{$project->id}");
        
        // Assuming your Controller checks authorizeProject() which throws a 403
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_any_project()
    {
        $org1 = Organisation::factory()->create();
        $org2 = Organisation::factory()->create();

        $superAdmin = User::factory()->create(['org_id' => $org1->id, 'role' => 'super_admin']);
        $project = Project::factory()->create(['org_id' => $org2->id]);

        $response = $this->actingAs($superAdmin)->getJson("/api/v1/projects/{$project->id}");
        
        $response->assertStatus(200);
    }
}
