<?php

use App\Models\Lesson;
use App\Models\User;

it('shows the home page with upcoming lessons and available places', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $lesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-11 09:00:00',
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
        ->assertSeeText('Welkom bij Sportcentrum De Linde')
        ->assertSeeText('Morning yoga')
        ->assertSeeText('1 / 8')
        ->assertSeeText('7');
});

it('shows an empty state when there are no upcoming lessons', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSeeText('Er zijn geen komende lessen.');
});
