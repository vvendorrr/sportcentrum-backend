<?php

use App\Models\Lesson;
use App\Models\User;

it('shows the home page with the weekday schedule and available places', function () {
    $this->travelTo('2026-10-12 07:00:00');
    $lesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-13 09:00:00',
        'max_participants' => 8,
    ]);

    $lesson->registrations()->create([
        'user_id' => User::factory()->create()->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertViewIs('home')
        ->assertSeeText('Welkom bij De Linde')
        ->assertSeeText('Inloggen')
        ->assertSeeText('Account aanmaken')
        ->assertSeeText('13 Di')
        ->assertSeeText('Morning yoga')
        ->assertSeeText('1 / 8 deelnemers')
        ->assertSee(route('lessons.show', $lesson), false)
        ->assertDontSeeText('Aantal deelnemers vandaag');
});

it('shows daily management statistics to admins', function () {
    $this->travelTo('2026-10-12 07:00:00');
    $admin = User::factory()->admin()->create(['name' => 'N. Pieter']);
    $member = User::factory()->create();
    $enrolledLesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-12 09:00:00',
        'max_participants' => 8,
    ]);
    $cancelledLesson = Lesson::factory()->create([
        'sport' => 'Evening spinning',
        'starts_at' => '2026-10-12 18:00:00',
        'max_participants' => 8,
    ]);

    $enrolledLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $cancelledLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'cancelled',
        'registered_at' => now()->subHour(),
        'canceled_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('home'));

    $response
        ->assertOk()
        ->assertSeeText('Goedemorgen, N. Pieter')
        ->assertSeeText('Aantal deelnemers vandaag')
        ->assertSeeText('Aantal afmeldingen vandaag')
        ->assertSeeText('Lessen vandaag')
        ->assertSeeText('Morning yoga')
        ->assertSeeText('Evening spinning');
});

it('shows an empty state when there are no lessons this week', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSeeText('Er zijn deze week geen lessen ingepland');
});
