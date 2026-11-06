<?php

namespace App\Support;

class DemoData
{
    public static function username(): string
    {
        return 'alexfit';
    }

    /**
     * @return array<int, array{name: string, date: string, time: string, category: string}>
     */
    public static function upcomingLessons(): array
    {
        return [
            [
                'name' => 'Spinning',
                'date' => now()->addDay()->toDateString(),
                'time' => '18:30',
                'category' => 'Cardio',
            ],
            [
                'name' => 'Circuittraining',
                'date' => now()->addDays(3)->toDateString(),
                'time' => '17:00',
                'category' => 'Kracht',
            ],
            [
                'name' => 'Yoga',
                'date' => now()->addDays(5)->toDateString(),
                'time' => '09:00',
                'category' => 'Ontspanning',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function lessonAttributes(): array
    {
        return [
            'starts_at' => now()->setTime(18, 30),
            'duration_minutes' => 60,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 20,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function account(): array
    {
        return [
            'account_name' => self::username(),
            'email' => 'alex.morgan@example.com',
            'first_name' => 'Alex',
            'last_name' => 'Morgan',
            'gender' => 'Non-binair',
            'birth_date' => '1995-04-12',
        ];
    }

    /**
     * @return array<int, array{sport: string, category: string, starts_at: string}>
     */
    public static function registeredLessons(): array
    {
        return [
            [
                'sport' => 'Spinning',
                'category' => 'Cardio',
                'starts_at' => now()->addDay()->setTime(18, 30)->toIso8601String(),
            ],
            [
                'sport' => 'Circuittraining',
                'category' => 'Kracht',
                'starts_at' => now()->addDays(3)->setTime(17, 0)->toIso8601String(),
            ],
            [
                'sport' => 'Yoga',
                'category' => 'Ontspanning',
                'starts_at' => now()->addDays(5)->setTime(9, 0)->toIso8601String(),
            ],
            [
                'sport' => 'Aquagym',
                'category' => 'Cardio',
                'starts_at' => now()->addWeek()->setTime(10, 30)->toIso8601String(),
            ],
        ];
    }

    /**
     * @return array<int, array{sport: string, category: string, schedule: string, time: string}>
     */
    public static function regularLessons(): array
    {
        return [
            [
                'sport' => 'Yoga',
                'category' => 'Ontspanning',
                'schedule' => 'Elke dinsdag',
                'time' => '09:00',
            ],
        ];
    }

    /**
     * @return array<int, array{id: int, name: string, email: string, phone_number: string, role: string, is_active: bool}>
     */
    public static function trainers(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Jamie Rivera',
                'email' => 'jamie.rivera@example.com',
                'phone_number' => '555-0101',
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Taylor Bennett',
                'email' => 'taylor.bennett@example.com',
                'phone_number' => '555-0102',
                'role' => 'member',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Jordan Kim',
                'email' => 'jordan.kim@example.com',
                'phone_number' => '555-0103',
                'role' => 'member',
                'is_active' => false,
            ],
        ];
    }

    /**
     * @return array<int, object{status: string}>
     */
    public static function lessonRegistrations(): array
    {
        return array_fill(0, 8, (object) ['status' => 'enrolled']);
    }
}
