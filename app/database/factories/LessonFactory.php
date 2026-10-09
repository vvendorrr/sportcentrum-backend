<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'instructor_id' => User::factory()->admin(),
            'starts_at' => fake()->dateTimeBetween('+1 day', '+2 weeks'),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'category' => fake()->randomElement(['strength', 'cardio', 'relaxation']),
            'sport' => fake()->randomElement(['Spinning', 'Yoga', 'Aquagym', 'Zwemmen', 'Circuittraining']),
            'max_participants' => fake()->randomElement([12, 16, 20]),
        ];
    }
}
