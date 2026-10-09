<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructor = User::query()->where('role', 'admin')->firstOrFail();

        foreach ([
            ['starts_at' => now()->addDay()->setTime(9, 0), 'duration_minutes' => 45, 'category' => 'cardio', 'sport' => 'Spinning', 'max_participants' => 16],
            ['starts_at' => now()->addDay()->setTime(11, 0), 'duration_minutes' => 60, 'category' => 'relaxation', 'sport' => 'Yoga', 'max_participants' => 12],
            ['starts_at' => now()->addDays(2)->setTime(10, 0), 'duration_minutes' => 45, 'category' => 'cardio', 'sport' => 'Aquagym', 'max_participants' => 20],
            ['starts_at' => now()->addDays(3)->setTime(18, 0), 'duration_minutes' => 60, 'category' => 'strength', 'sport' => 'Circuittraining', 'max_participants' => 16],
            ['starts_at' => now()->addDays(4)->setTime(9, 30), 'duration_minutes' => 60, 'category' => 'cardio', 'sport' => 'Zwemmen', 'max_participants' => 20],
        ] as $lesson) {
            Lesson::query()->create([
                ...$lesson,
                'instructor_id' => $instructor->id,
            ]);
        }
    }
}
