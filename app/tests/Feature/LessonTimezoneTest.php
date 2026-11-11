<?php

use App\Models\Lesson;
use App\Models\User;

it('stores admin-entered lesson times in UTC while displaying Dutch local time', function () {
    $this->travelTo('2026-10-10 08:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->trainer()->create();

    $response = $this->actingAs($admin)
        ->post(route('lessons.store'), [
            'instructor_id' => $trainer->id,
            'starts_at' => '2026-10-11T09:00',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Spinning lokaal',
            'max_participants' => 16,
        ]);

    $lesson = Lesson::query()->where('sport', 'Spinning lokaal')->firstOrFail();

    $response->assertRedirectToRoute('lessons.show', $lesson);
    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'starts_at' => '2026-10-11 07:00:00',
    ]);

    $this->get(route('lessons.show', $lesson))
        ->assertOk()
        ->assertSeeText('11-10-2026 09:00 (Nederlandse tijd)')
        ->assertDontSeeText('UTC');

    $this->actingAs($admin)
        ->get(route('lessons.edit', $lesson))
        ->assertOk()
        ->assertSee('value="2026-10-11T09:00"', false);
});

it('shows local time in the account page registration list', function () {
    $this->travelTo('2026-10-10 08:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'sport' => 'Vroege spinning',
        'starts_at' => '2026-10-11 07:00:00',
    ]);
    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $this->actingAs($member)
        ->get(route('account.show'))
        ->assertOk()
        ->assertSeeText('11 oktober 2026')
        ->assertSeeText('09:00');
});

it('groups lessons by the Dutch calendar week even when their UTC date is the previous day', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $lesson = Lesson::factory()->create([
        'sport' => 'Maandagochtendles',
        'starts_at' => '2026-10-11 22:30:00',
    ]);

    $this->get(route('lessons.index', ['week' => '2026-10-12']))
        ->assertOk()
        ->assertViewHas('lessons', fn ($lessons) => $lessons->contains('id', $lesson->id))
        ->assertViewHas('lessonsByDay', fn ($days) => $days->has('2026-10-12'));
});

it('rejects a local time that does not exist when daylight saving time starts', function () {
    $this->travelTo('2026-03-28 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->trainer()->create();

    $this->actingAs($admin)
        ->from(route('lessons.create'))
        ->post(route('lessons.store'), [
            'instructor_id' => $trainer->id,
            'starts_at' => '2026-03-29T02:30',
            'duration_minutes' => 45,
            'category' => 'cardio',
            'sport' => 'Zomertijdles',
            'max_participants' => 16,
        ])
        ->assertRedirectBackWithErrors('starts_at');

    $this->assertDatabaseMissing('lessons', ['sport' => 'Zomertijdles']);
});
