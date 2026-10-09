<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Sportcentrum Beheerder',
            'email' => 'beheerder@example.com',
        ]);

        User::factory()->count(8)->create();

        $this->call([
            LessonSeeder::class,
            LessonRegistrationSeeder::class,
        ]);
    }
}
