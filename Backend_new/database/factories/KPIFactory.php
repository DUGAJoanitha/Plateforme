<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KPI;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KPI>
 */
class KPIFactory extends Factory
{
    protected $model = KPI::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'target_value' => fake()->randomFloat(2, 100, 1000),
            'current_value' => fake()->randomFloat(2, 0, 100),
            'baseline' => 0,
            'unit' => fake()->randomElement(['percentage', 'count', 'amount']),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'quarterly', 'yearly']),
        ];
    }
}
