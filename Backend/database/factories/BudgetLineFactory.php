<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BudgetLine;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetLine>
 */
class BudgetLineFactory extends Factory
{
    protected $model = BudgetLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'allocated' => fake()->randomFloat(2, 5000, 50000),
            'spent' => fake()->randomFloat(2, 0, 5000),
            'category' => fake()->word(),
            'description' => fake()->sentence(),
        ];
    }
}
