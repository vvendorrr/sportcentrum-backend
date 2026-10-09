<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonRegistration>
 */
class LessonRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory(),
            'status' => 'enrolled',
            'registered_at' => now(),
            'attendance' => null,
            'canceled_at' => null,
        ];
    }
}
