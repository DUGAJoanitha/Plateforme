<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Risk;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Risk>
 */
class RiskFactory extends Factory
{
    protected $model = Risk::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'description' => fake()->sentence(),
            'probability' => fake()->numberBetween(1, 5),
            'impact' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(['identified', 'monitored', 'mitigated', 'closed']),
            'mitigation' => fake()->paragraph(),
        ];
    }
}
