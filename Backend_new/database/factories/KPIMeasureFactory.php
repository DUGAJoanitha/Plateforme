<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KPIMeasure;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KPIMeasure>
 */
class KPIMeasureFactory extends Factory
{
    protected $model = KPIMeasure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->randomFloat(2, 0, 200),
            'notes' => fake()->sentence(),
            'collected_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
