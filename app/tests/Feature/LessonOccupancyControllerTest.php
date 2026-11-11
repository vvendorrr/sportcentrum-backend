<?php

use App\Models\Lesson;
use App\Models\User;

it('shows upcoming lesson occupancy to admins without exposing participant names', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->trainer()->create(['name' => 'Assigned Trainer']);
    $member = User::factory()->create(['name' => 'Private Member Name']);
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Spinning',
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 2,
    ]);
    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $lesson->registrations()->create([
        'user_id' => User::factory()->create()->id,
        'status' => 'waitlisted',
        'registered_at' => now(),
    ]);
    $lowOccupancyLesson = Lesson::factory()->create([
        'sport' => 'Circuittraining',
        'starts_at' => '2026-10-12 09:00:00',
        'max_participants' => 4,
    ]);
    $lowOccupancyLesson->registrations()->create([
        'user_id' => User::factory()->create()->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    Lesson::factory()->create([
        'sport' => 'Past lesson',
        'starts_at' => '2026-10-09 09:00:00',
    ]);

    $this->actingAs($admin)
        ->get(route('lessons.occupancy'))
        ->assertOk()
        ->assertSeeText('Spinning')
        ->assertSeeText('Assigned Trainer')
        ->assertSeeText('Ingeschreven')
        ->assertSeeText('Vrije plekken')
        ->assertSeeText('Beschikbaar')
        ->assertSeeText('Lage bezetting')
        ->assertSeeText('Circuittraining')
        ->assertSeeText('50%')
        ->assertDontSeeText('Private Member Name')
        ->assertDontSeeText('Past lesson');
});

it('forbids non-admin users and guests from viewing lesson occupancy', function () {
    $this->get(route('lessons.occupancy'))
        ->assertRedirect(route('home'));

    $this->actingAs(User::factory()->create())
        ->get(route('lessons.occupancy'))
        ->assertForbidden();

    $this->actingAs(User::factory()->trainer()->create())
        ->get(route('lessons.occupancy'))
        ->assertForbidden();
});
