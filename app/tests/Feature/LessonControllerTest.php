<?php

use App\Models\Lesson;
use App\Models\User;

it('allows an admin to create a lesson with a trainer instructor', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->trainer()->create();

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

    $lesson = Lesson::query()->firstOrFail();

    $response
        ->assertRedirectToRoute('lessons.show', $lesson)
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

it('forbids members and trainers from creating a lesson', function () {
    foreach ([User::factory()->create(), User::factory()->trainer()->create()] as $user) {
        $this->actingAs($user)
            ->post(route('lessons.store'), [])
            ->assertForbidden();
    }

    $this->assertDatabaseCount('lessons', 0);
});

it('rejects a lesson with zero capacity', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->trainer()->create();

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

it('rejects an admin as the lesson instructor because only trainers may teach lessons', function () {
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

    $response->assertRedirectBackWithErrors(['instructor_id']);
    expect(Lesson::query()->count())->toBe(0);
});

it('rejects an inactive trainer as the lesson instructor', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $inactiveTrainer = User::factory()->trainer()->inactive()->create();

    $response = $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), [
            'instructor_id' => $inactiveTrainer->id,
            'starts_at' => '2026-10-11 09:00:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 16,
        ]);

    $response->assertRedirectBackWithErrors(['instructor_id']);
    expect(Lesson::query()->count())->toBe(0);
});

it('only offers active trainers in the lesson creation form', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->trainer()->create(['name' => 'Actieve Trainer']);
    User::factory()->trainer()->inactive()->create(['name' => 'Inactieve Trainer']);

    $this->actingAs($admin)
        ->get(route('lessons.create'))
        ->assertOk()
        ->assertSeeText('Actieve Trainer')
        ->assertDontSeeText('Inactieve Trainer');
});

it('redirects guests away from lesson management', function () {
    $response = $this->get(route('lessons.create'));

    $response->assertRedirect(route('home'));
});

it('renders the lesson creation form for an admin', function () {
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->trainer()->create(['name' => 'Lesson Instructor']);

    $response = $this->actingAs($admin)->get(route('lessons.create'));

    $response
        ->assertOk()
        ->assertSeeText('Les aanmaken')
        ->assertSeeText('Lesson Instructor');
});

it('renders the weekly roster and links lessons to their detail pages for members', function () {
    $this->travelTo('2026-10-12 09:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'sport' => 'Morning yoga',
        'starts_at' => '2026-10-13 09:00:00',
    ]);

    $response = $this->actingAs($member)->get(route('lessons.index'));

    $response
        ->assertOk()
        ->assertSeeText('Morning yoga')
        ->assertSee(route('lessons.show', $lesson), false);
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
    $this->travelTo('2026-10-12 09:00:00');
    $lesson = Lesson::factory()->create([
        'sport' => 'Public yoga',
        'starts_at' => '2026-10-13 09:00:00',
    ]);
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
        ->assertDontSeeText('Private Member')
        ->assertSee(route('lessons.show', $lesson), false);
});

it('filters the weekly roster by category, trainer and sport', function () {
    $this->travelTo('2026-10-12 09:00:00');
    $selectedTrainer = User::factory()->trainer()->create();
    $otherTrainer = User::factory()->trainer()->create();
    $selectedLesson = Lesson::factory()->create([
        'instructor_id' => $selectedTrainer->id,
        'starts_at' => '2026-10-13 09:00:00',
        'category' => 'relaxation',
        'sport' => 'Morning yoga',
    ]);
    Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'starts_at' => '2026-10-14 10:00:00',
        'category' => 'cardio',
        'sport' => 'Spinning',
    ]);

    $response = $this->get(route('lessons.index', [
        'week' => '2026-10-12',
        'category' => 'relaxation',
        'instructor_id' => $selectedTrainer->id,
        'sport' => 'yoga',
    ]));

    $response
        ->assertOk()
        ->assertSeeText($selectedLesson->sport)
        ->assertDontSeeText('Spinning');
});

it('shows lesson details to visitors without showing member names', function () {
    $lesson = Lesson::factory()->create(['sport' => 'Public yoga']);
    $member = User::factory()->create(['name' => 'Private Member']);
    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $response = $this->get(route('lessons.show', $lesson));

    $response
        ->assertOk()
        ->assertSeeText('Public yoga')
        ->assertSeeText('Trainer')
        ->assertDontSeeText('Private Member')
        ->assertSeeText('Log in om je in te schrijven');
});

it('shows the cancel action for enrolled and waitlisted members before the deadline', function (string $status) {
    $this->travelTo('2026-10-10 10:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
        'max_participants' => 1,
    ]);
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => $status,
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($member)->get(route('lessons.show', $lesson));

    $response
        ->assertOk()
        ->assertSeeText('Afmelden voor deze les')
        ->assertSee(route('registrations.cancel', $registration), false);
})->with([
    'ingeschreven' => 'enrolled',
    'op de wachtlijst' => 'waitlisted',
]);

it('hides the cancel action after the cancellation deadline', function () {
    $this->travelTo('2026-10-10 10:00:01');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
    ]);
    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($member)->get(route('lessons.show', $lesson));

    $response
        ->assertOk()
        ->assertSeeText('De afmeldtermijn is verstreken.')
        ->assertDontSeeText('Afmelden voor deze les');
});

it('shows edit and delete actions to admins on lesson details', function () {
    $admin = User::factory()->admin()->create();
    $lesson = Lesson::factory()->create();

    $response = $this->actingAs($admin)->get(route('lessons.show', $lesson));

    $response
        ->assertOk()
        ->assertSeeText('Les bewerken')
        ->assertSeeText('Les verwijderen');
});

it('shows the participants link to the assigned trainer but not lesson management actions', function () {
    $trainer = User::factory()->trainer()->create();
    $otherTrainer = User::factory()->trainer()->create();
    $lesson = Lesson::factory()->create(['instructor_id' => $trainer->id]);

    $this->actingAs($trainer)
        ->get(route('lessons.show', $lesson))
        ->assertOk()
        ->assertSee(route('lessons.participants', $lesson), false)
        ->assertSeeText('Deelnemers en aanwezigheid')
        ->assertDontSeeText('Les bewerken')
        ->assertDontSeeText('Les verwijderen');

    $this->actingAs($otherTrainer)
        ->get(route('lessons.show', $lesson))
        ->assertOk()
        ->assertDontSee(route('lessons.participants', $lesson), false);
});

it('does not show lesson management actions to members', function () {
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create();

    $response = $this->actingAs($member)->get(route('lessons.show', $lesson));

    $response
        ->assertOk()
        ->assertDontSeeText('Les bewerken')
        ->assertDontSeeText('Les verwijderen');
});

it('allows an admin to update a lesson', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->trainer()->create();
    $lesson = Lesson::factory()->create([
        'sport' => 'Old lesson name',
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 10,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('lessons.edit', $lesson))
        ->patch(route('lessons.update', $lesson), [
            'instructor_id' => $instructor->id,
            'starts_at' => '2026-10-12 10:00:00',
            'duration_minutes' => 60,
            'category' => 'strength',
            'sport' => 'New lesson name',
            'max_participants' => 12,
        ]);

    $response->assertRedirectToRoute('lessons.show', $lesson);
    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'instructor_id' => $instructor->id,
        'sport' => 'New lesson name',
        'max_participants' => 12,
    ]);
});

it('does not allow an admin to reduce capacity below current enrollment', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->trainer()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 2,
    ]);
    $members = User::factory()->count(2)->create();

    foreach ($members as $member) {
        $lesson->registrations()->create([
            'user_id' => $member->id,
            'status' => 'enrolled',
            'registered_at' => now(),
        ]);
    }

    $response = $this->actingAs($admin)
        ->from(route('lessons.edit', $lesson))
        ->patch(route('lessons.update', $lesson), [
            'instructor_id' => $instructor->id,
            'starts_at' => '2026-10-11 09:00:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning',
            'max_participants' => 1,
        ]);

    $response->assertRedirectBackWithErrors([
        'max_participants' => 'De capaciteit kan niet lager zijn dan het huidige aantal ingeschreven leden.',
    ]);
    expect($lesson->fresh()->max_participants)->toBe(2);
});

it('forbids a member from deleting a lesson', function () {
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create();

    $response = $this->actingAs($member)->delete(route('lessons.destroy', $lesson));

    $response->assertForbidden();
    $this->assertModelExists($lesson);
});

it('allows an admin to delete a lesson and its registrations', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create();
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($admin)->delete(route('lessons.destroy', $lesson));

    $response
        ->assertRedirectToRoute('lessons.index')
        ->assertSessionHas('status', 'De les en bijbehorende inschrijvingen zijn verwijderd.');
    $this->assertModelMissing($lesson);
    $this->assertModelMissing($registration);
});
