<?php

namespace Tests\Unit\Models;

use App\Models\KPI;
use App\Models\KPIMeasure;
use App\Models\Project;
use PHPUnit\Framework\TestCase;
use Tests\TestCase as BaseTestCase;

class KPITest extends BaseTestCase
{
    /**
     * Test KPI calculation of performance percentage
     */
    public function test_calculate_performance(): void
    {
        $kpi = KPI::factory()->create([
            'target_value' => 100,
            'current_value' => 75,
        ]);

        $performance = $kpi->calculatePerformance();
        
        $this->assertEquals(75, $performance);
        $this->assertTrue($kpi->isOnTrack());
    }

    /**
     * Test KPI on-track status (>= 70%)
     */
    public function test_is_on_track(): void
    {
        $kpiOnTrack = KPI::factory()->create([
            'target_value' => 100,
            'current_value' => 75,
        ]);
        
        $kpiNotOnTrack = KPI::factory()->create([
            'target_value' => 100,
            'current_value' => 60,
        ]);

        $this->assertTrue($kpiOnTrack->isOnTrack());
        $this->assertFalse($kpiNotOnTrack->isOnTrack());
    }

    /**
     * Test KPI with zero target value
     */
    public function test_calculate_performance_zero_target(): void
    {
        $kpi = KPI::factory()->create([
            'target_value' => 0,
            'current_value' => 0,
        ]);

        $performance = $kpi->calculatePerformance();
        
        $this->assertEquals(0, $performance);
    }

    /**
     * Test KPI exceeds target
     */
    public function test_performance_exceeds_target(): void
    {
        $kpi = KPI::factory()->create([
            'target_value' => 100,
            'current_value' => 150,
        ]);

        $performance = $kpi->calculatePerformance();
        
        $this->assertEquals(150, $performance);
    }

    /**
     * Test KPI has many measures
     */
    public function test_kpi_has_many_measures(): void
    {
        $kpi = KPI::factory()->create();
        
        KPIMeasure::factory(5)->create([
            'kpi_id' => $kpi->id,
        ]);

        $this->assertCount(5, $kpi->measures);
    }

    /**
     * Test KPI belongs to project
     */
    public function test_kpi_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $kpi = KPI::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertTrue($kpi->project->is($project));
    }
}
