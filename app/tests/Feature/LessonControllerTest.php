<?php

use App\Models\Lesson;
use App\Models\User;

it('allows an admin to create a lesson with an admin instructor', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), [
            'instructor_id' => $instructor->id,
            'starts_at' => '2026-10-11 09:00:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 16,
        ]);

    $response
        ->assertRedirectToRoute('lessons.index')
        ->assertSessionHas('status', 'Les aangemaakt.');

    $this->assertDatabaseHas('lessons', [
        'instructor_id' => $instructor->id,
        'starts_at' => '2026-10-11 09:00:00',
        'duration_minutes' => 45,
        'category' => 'cardio',
        'sport' => 'Spinning',
        'max_participants' => 16,
    ]);
});

it('forbids a member from creating a lesson', function () {
    $member = User::factory()->create();

    $response = $this->actingAs($member)
        ->post(route('lessons.store'), []);

    $response->assertForbidden();
    $this->assertDatabaseCount('lessons', 0);
});

it('rejects a lesson with zero capacity', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), [
            'instructor_id' => $instructor->id,
            'starts_at' => '2026-10-11 09:00:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 0,
        ]);

    $response->assertRedirectBackWithErrors([
        'max_participants' => 'De maximale capaciteit moet tussen 1 en 65.535 deelnemers liggen.',
    ]);
    expect(Lesson::query()->count())->toBe(0);
});

it('rejects a member as the lesson instructor', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $response = $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), [
            'instructor_id' => $member->id,
            'starts_at' => '2026-10-11 09:00:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 16,
        ]);

    $response->assertRedirectBackWithErrors(['instructor_id']);
    expect(Lesson::query()->count())->toBe(0);
});

it('redirects guests away from lesson management', function () {
    $response = $this->get(route('lessons.create'));

    $response->assertRedirect(route('home'));
});

it('renders the lesson creation form for an admin', function () {
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->admin()->create(['name' => 'Lesson Instructor']);

    $response = $this->actingAs($admin)->get(route('lessons.create'));

    $response
        ->assertOk()
        ->assertSeeText('Les aanmaken')
        ->assertSeeText('Lesson Instructor');
});

it('renders upcoming lessons with a registration action for members', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-11 09:00:00',
    ]);

    $response = $this->actingAs($member)->get(route('lessons.index'));

    $response
        ->assertOk()
        ->assertSeeText('Morning yoga')
        ->assertSeeText('Inschrijven');
});

it('rejects missing required lesson fields', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), []);

    $response->assertRedirectBackWithErrors([
        'instructor_id',
        'starts_at',
        'duration_minutes',
        'category',
        'sport',
        'max_participants',
    ]);
    $this->assertDatabaseCount('lessons', 0);
});

it('makes the lesson schedule public without exposing member names', function () {
    $lesson = Lesson::factory()->create(['sport' => 'Public yoga']);
    $member = User::factory()->create(['name' => 'Private Member']);
    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $response = $this->get(route('lessons.index'));

    $response
        ->assertOk()
        ->assertSeeText('Public yoga')
        ->assertDontSeeText('Private Member');
});
