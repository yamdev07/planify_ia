<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'deadline'    => $this->faker->optional()->dateTimeBetween('+1 week', '+6 months')?->format('Y-m-d'),
            'status'      => $this->faker->randomElement(['active', 'completed', 'abandoned']),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}
