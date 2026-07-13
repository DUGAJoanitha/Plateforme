<?php

namespace Tests\Unit\Models;

use App\Models\Risk;
use App\Models\Project;
use Tests\TestCase;

class RiskTest extends TestCase
{
    /**
     * Test risk level calculation - CRITICAL
     */
    public function test_get_risk_level_critical(): void
    {
        $risk = Risk::factory()->create([
            'probability' => 5,
            'impact' => 5,
        ]);

        $this->assertEquals('CRITICAL', $risk->getRiskLevel());
    }

    /**
     * Test risk level calculation - HIGH
     */
    public function test_get_risk_level_high(): void
    {
        $risk = Risk::factory()->create([
            'probability' => 4,
            'impact' => 4,
        ]);

        $this->assertEquals('HIGH', $risk->getRiskLevel());
    }

    /**
     * Test risk level calculation - MEDIUM
     */
    public function test_get_risk_level_medium(): void
    {
        $risk = Risk::factory()->create([
            'probability' => 3,
            'impact' => 3,
        ]);

        $this->assertEquals('MEDIUM', $risk->getRiskLevel());
    }

    /**
     * Test risk level calculation - LOW
     */
    public function test_get_risk_level_low(): void
    {
        $risk = Risk::factory()->create([
            'probability' => 1,
            'impact' => 1,
        ]);

        $this->assertEquals('LOW', $risk->getRiskLevel());
    }

    /**
     * Test risk score calculation
     */
    public function test_risk_score_calculation(): void
    {
        $risk = Risk::factory()->create([
            'probability' => 4,
            'impact' => 3,
        ]);

        $expectedScore = 4 * 3;
        
        $this->assertEquals($expectedScore, $risk->score);
    }

    /**
     * Test risk belongs to project
     */
    public function test_risk_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $risk = Risk::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertTrue($risk->project->is($project));
    }

    /**
     * Test risk status enum
     */
    public function test_risk_status_values(): void
    {
        $riskActive = Risk::factory()->create(['status' => 'monitored']);
        $riskMitigated = Risk::factory()->create(['status' => 'mitigated']);
        $riskClosed = Risk::factory()->create(['status' => 'closed']);

        $this->assertEquals('monitored', $riskActive->status);
        $this->assertEquals('mitigated', $riskMitigated->status);
        $this->assertEquals('closed', $riskClosed->status);
    }
}
