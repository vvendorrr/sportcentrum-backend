<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessons = Lesson::query()->orderBy('starts_at')->take(3)->get();
        $members = User::query()->where('role', 'member')->get();

        foreach ($members as $member) {
            foreach ($lessons as $lesson) {
                LessonRegistration::query()->create([
                    'lesson_id' => $lesson->id,
                    'user_id' => $member->id,
                    'status' => 'enrolled',
                    'registered_at' => now(),
                ]);
            }
        }
    }
}
