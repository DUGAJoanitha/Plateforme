<?php

namespace Tests\Unit\Models;

use App\Models\BudgetLine;
use App\Models\Expense;
use App\Models\Project;
use Tests\TestCase;

class BudgetLineTest extends TestCase
{
    /**
     * Test budget line balance calculation
     */
    public function test_get_balance(): void
    {
        $budgetLine = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 300,
        ]);

        $balance = $budgetLine->getBalance();
        
        $this->assertEquals(700, $balance);
    }

    /**
     * Test budget line consumption percentage
     */
    public function test_get_consumption_percentage(): void
    {
        $budgetLine = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 500,
        ]);

        $consumption = $budgetLine->getConsumptionPercentage();
        
        $this->assertEquals(50, $consumption);
    }

    /**
     * Test budget line is over budget
     */
    public function test_is_over_budget(): void
    {
        $overBudget = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 1500,
        ]);
        
        $underBudget = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 800,
        ]);

        $this->assertTrue($overBudget->isOverBudget());
        $this->assertFalse($underBudget->isOverBudget());
    }

    /**
     * Test budget alert trigger at threshold
     */
    public function test_should_trigger_alert(): void
    {
        $budgetLine = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 900,
            'alert_threshold' => 90,
        ]);

        $this->assertTrue($budgetLine->shouldTriggerAlert());
    }

    /**
     * Test budget alert not triggered below threshold
     */
    public function test_should_not_trigger_alert(): void
    {
        $budgetLine = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 850,
            'alert_threshold' => 90,
        ]);

        $this->assertFalse($budgetLine->shouldTriggerAlert());
    }

    /**
     * Test budget line consumption at 100%
     */
    public function test_consumption_at_100_percent(): void
    {
        $budgetLine = BudgetLine::factory()->create([
            'allocated' => 1000,
            'spent' => 1000,
        ]);

        $this->assertEquals(100, $budgetLine->getConsumptionPercentage());
        $this->assertEquals(0, $budgetLine->getBalance());
    }

    /**
     * Test budget line belongs to project
     */
    public function test_budget_line_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $budgetLine = BudgetLine::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertTrue($budgetLine->project->is($project));
    }

    /**
     * Test budget line has many expenses
     */
    public function test_budget_line_has_many_expenses(): void
    {
        $budgetLine = BudgetLine::factory()->create();
        
        Expense::factory(3)->create([
            'budget_line_id' => $budgetLine->id,
        ]);

        $this->assertCount(3, $budgetLine->expenses);
    }
}
