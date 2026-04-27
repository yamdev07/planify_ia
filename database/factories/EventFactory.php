<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end   = (clone $start)->modify('+' . $this->faker->numberBetween(30, 120) . ' minutes');

        return [
            'user_id'         => User::factory(),
            'title'           => $this->faker->sentence(3),
            'start_at'        => $start,
            'end_at'          => $end,
            'is_recurring'    => false,
            'recurrence_rule' => null,
            'color'           => $this->faker->optional()->hexColor(),
        ];
    }
}
