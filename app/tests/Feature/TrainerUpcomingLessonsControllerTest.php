<?php

use App\Models\Lesson;
use App\Models\User;

it('shows a trainer their upcoming lessons with enrolled and waitlisted attendees', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $trainer = User::factory()->trainer()->create();
    $member = User::factory()->create(['name' => 'Confirmed Member']);
    $waitlistedMember = User::factory()->create(['name' => 'Waitlisted Member']);
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 1,
    ]);
    $lesson->registrations()->createMany([
        [
            'user_id' => $member->id,
            'status' => 'enrolled',
            'registered_at' => now(),
        ],
        [
            'user_id' => $waitlistedMember->id,
            'status' => 'waitlisted',
            'registered_at' => now()->addSecond(),
        ],
    ]);
    Lesson::factory()->create([
        'instructor_id' => User::factory()->trainer(),
        'sport' => 'Another trainer’s lesson',
        'starts_at' => '2026-10-11 10:00:00',
    ]);
    Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Past lesson',
        'starts_at' => '2026-10-09 10:00:00',
    ]);

    $this->actingAs($trainer)
        ->get(route('trainer-lessons.upcoming'))
        ->assertOk()
        ->assertSeeText('Morning yoga')
        ->assertSeeText('Confirmed Member')
        ->assertSeeText('Waitlisted Member')
        ->assertSeeText('Bevestigde deelnemers')
        ->assertSeeText('Wachtlijstdeelnemers')
        ->assertDontSeeText('Another trainer’s lesson')
        ->assertDontSeeText('Past lesson');
});

it('forbids members, admins, and guests from viewing the trainer lesson list', function () {
    $this->get(route('trainer-lessons.upcoming'))
        ->assertRedirect(route('home'));

    $this->actingAs(User::factory()->create())
        ->get(route('trainer-lessons.upcoming'))
        ->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('trainer-lessons.upcoming'))
        ->assertForbidden();
});
