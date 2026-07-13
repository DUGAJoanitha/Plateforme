<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Project;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    /**
     * Test activity is blocked when dependency not completed
     */
    public function test_activity_is_blocked_when_dependency_incomplete(): void
    {
        $project = Project::factory()->create();
        
        $parentActivity = Activity::factory()->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);
        
        $childActivity = Activity::factory()->create([
            'project_id' => $project->id,
            'depends_on' => $parentActivity->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($childActivity->isBlocked());
    }

    /**
     * Test activity is not blocked when dependency completed
     */
    public function test_activity_is_not_blocked_when_dependency_completed(): void
    {
        $project = Project::factory()->create();
        
        $parentActivity = Activity::factory()->create([
            'project_id' => $project->id,
            'status' => 'completed',
        ]);
        
        $childActivity = Activity::factory()->create([
            'project_id' => $project->id,
            'depends_on' => $parentActivity->id,
            'status' => 'pending',
        ]);

        $this->assertFalse($childActivity->isBlocked());
    }

    /**
     * Test mark activity complete
     */
    public function test_mark_activity_complete(): void
    {
        $activity = Activity::factory()->create([
            'status' => 'in_progress',
            'progress' => 90,
        ]);

        $activity->markComplete();

        $this->assertEquals('completed', $activity->status);
        $this->assertEquals(100, $activity->progress);
    }

    /**
     * Test activity is not blocked without dependency
     */
    public function test_activity_without_dependency_not_blocked(): void
    {
        $activity = Activity::factory()->create([
            'depends_on' => null,
            'status' => 'pending',
        ]);

        $this->assertFalse($activity->isBlocked());
    }

    /**
     * Test activity belongs to project
     */
    public function test_activity_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $activity = Activity::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertTrue($activity->project->is($project));
    }

    /**
     * Test activity progress tracking
     */
    public function test_activity_progress_tracking(): void
    {
        $activity = Activity::factory()->create([
            'progress' => 0,
            'status' => 'pending',
        ]);

        $this->assertEquals(0, $activity->progress);

        $activity->update(['progress' => 50, 'status' => 'in_progress']);
        $this->assertEquals(50, $activity->progress);
    }
}
