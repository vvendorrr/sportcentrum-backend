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

        User::factory()->admin()->create([
            'name' => 'R. de Wit',
            'email' => 'r.dewit@example.com',
        ]);

        User::factory()->admin()->count(4)->create();
        User::factory()->trainer()->count(4)->create();

        $this->call(DemoMemberSeeder::class);

        User::factory()->count(8)->create();

        $this->call([
            LessonSeeder::class,
            LessonRegistrationSeeder::class,
        ]);
    }
}
