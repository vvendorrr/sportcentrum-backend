<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'deelnemer@example.com'],
            [
                'name' => 'Demo Deelnemer',
                'password_hash' => 'LindeLid123!',
                'role' => 'member',
                'is_active' => true,
            ],
        );
    }
}
