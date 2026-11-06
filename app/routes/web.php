<?php

use App\Models\Lesson;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'username' => 'alexfit',
        'lessons' => [
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
        ],
    ]);
});

Route::get('/lessons/create', function () {
    return view('lessons.create');
})->name('lessons.create');

Route::get('/lessons/preview', function () {
    $lesson = Lesson::make([
        'starts_at' => now()->setTime(18, 30),
        'duration_minutes' => 60,
        'category' => 'cardio',
        'sport' => 'Spinning',
        'max_participants' => 20,
    ]);

    $lesson->setRelation('registrations', collect([
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
        (object) ['status' => 'enrolled'],
    ]));

    return view('lessons.details', ['lesson' => $lesson]);
})->name('lessons.preview');

Route::get('/account', function () {
    return view('users.account', [
        'account' => [
            'account_name' => 'alexfit',
            'email' => 'alex.morgan@example.com',
            'first_name' => 'Alex',
            'last_name' => 'Morgan',
            'gender' => 'Non-binair',
            'birth_date' => '1995-04-12',
        ],
        'registeredLessons' => [
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
        ],
        'regularLessons' => [
            [
                'sport' => 'Yoga',
                'category' => 'Ontspanning',
                'schedule' => 'Elke dinsdag',
                'time' => '09:00',
            ],
        ],
    ]);
})->name('account');

Route::get('/admin/trainers', function () {
    $trainers = [
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

    return view('users.admin-trainers', [
        'trainer' => $trainers[0],
        'trainers' => $trainers,
    ]);
})->name('admin.trainers');
