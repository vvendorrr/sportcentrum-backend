<?php

use App\Models\Lesson;
use App\Models\User;

it('shows a selected participant’s active registrations and contact information to admins', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->create([
        'name' => 'N. Pieter',
        'email' => 'n.pieter@example.com',
    ]);
    $otherParticipant = User::factory()->create(['name' => 'Other Participant']);
    $enrolledLesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-11 09:00:00',
    ]);
    $waitlistedLesson = Lesson::factory()->create([
        'sport' => 'Evening spinning',
        'starts_at' => '2026-10-12 18:00:00',
    ]);
    $cancelledLesson = Lesson::factory()->create(['sport' => 'Cancelled class']);
    $otherLesson = Lesson::factory()->create(['sport' => 'Another member’s class']);

    $enrolledLesson->registrations()->create([
        'user_id' => $participant->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $waitlistedLesson->registrations()->create([
        'user_id' => $participant->id,
        'status' => 'waitlisted',
        'registered_at' => now(),
    ]);
    $cancelledLesson->registrations()->create([
        'user_id' => $participant->id,
        'status' => 'cancelled',
        'registered_at' => now(),
        'canceled_at' => now(),
    ]);
    $otherLesson->registrations()->create([
        'user_id' => $otherParticipant->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('participants.index', ['participant' => $participant->id]));

    $response
        ->assertOk()
        ->assertSeeText('N. Pieter')
        ->assertSeeText('n.pieter@example.com')
        ->assertSeeText('Morning yoga')
        ->assertSeeText('Evening spinning')
        ->assertSeeText('Wachtlijst')
        ->assertDontSeeText('Cancelled class')
        ->assertDontSeeText('Another member’s class');
});

it('shows only upcoming lessons for a trainer and their contact information', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create([
        'name' => 'D. Jones',
        'email' => 'd.jones@example.com',
    ]);
    $otherTrainer = User::factory()->admin()->create(['name' => 'Another Trainer']);
    User::factory()->admin()->create(['name' => 'Trainer without lessons']);
    Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Upcoming yoga',
        'starts_at' => '2026-10-11 09:00:00',
    ]);
    Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Later spinning',
        'starts_at' => '2026-10-12 10:00:00',
    ]);
    Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Past class',
        'starts_at' => '2026-10-09 10:00:00',
    ]);
    Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'sport' => 'Other trainer’s class',
        'starts_at' => '2026-10-11 11:00:00',
    ]);

    $response = $this->actingAs($admin)->get(route('trainers.index', ['trainer' => $trainer->id]));

    $response
        ->assertOk()
        ->assertSeeText('D. Jones')
        ->assertSeeText('d.jones@example.com')
        ->assertSeeText('Trainer without lessons')
        ->assertSeeText('Upcoming yoga')
        ->assertSeeText('Later spinning')
        ->assertDontSeeText('Past class')
        ->assertDontSeeText('Other trainer’s class');
});

it('restricts participant and trainer directories to admins', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get(route('participants.index'))
        ->assertForbidden();

    $this->actingAs($member)
        ->get(route('trainers.index'))
        ->assertForbidden();
});

it('redirects guests from the participant and trainer directories to sign in', function () {
    $this->get(route('participants.index'))->assertRedirect(route('home'));
    $this->get(route('trainers.index'))->assertRedirect(route('home'));
});
