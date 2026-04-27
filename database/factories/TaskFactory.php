<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'goal_id'          => null,
            'title'            => $this->faker->sentence(5),
            'description'      => $this->faker->optional()->paragraph(),
            'priority'         => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'status'           => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'scheduled_at'     => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'duration_minutes' => $this->faker->optional()->randomElement([15, 30, 45, 60, 90, 120]),
            'category'         => $this->faker->optional()->randomElement(['Travail', 'Personnel', 'Étude']),
        ];
    }

    public function urgent(): static
    {
        return $this->state(['priority' => 'urgent', 'status' => 'todo']);
    }

    public function done(): static
    {
        return $this->state(['status' => 'done']);
    }
}
