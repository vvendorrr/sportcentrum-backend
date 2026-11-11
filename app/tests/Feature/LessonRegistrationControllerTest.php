<?php

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;

it('enrolls a member when the lesson has an available place', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 2,
    ]);

    $response = $this->actingAs($member)
        ->post(route('lessons.register', $lesson));

    $response
        ->assertRedirectToRoute('lessons.index')
        ->assertSessionHas('status', 'Je bent ingeschreven voor de les.');

    $this->assertDatabaseHas('lesson_registrations', [
        'lesson_id' => $lesson->id,
        'user_id' => $member->id,
        'status' => 'enrolled',
    ]);
});

it('waitlists a member when the lesson is full', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 1,
    ]);
    $enrolledMember = User::factory()->create();

    $lesson->registrations()->create([
        'user_id' => $enrolledMember->id,
        'status' => 'enrolled',
        'registered_at' => now()->subMinute(),
    ]);

    $response = $this->actingAs($member)
        ->post(route('lessons.register', $lesson));

    $response->assertSessionHas('status', 'De les is vol. Je bent op de wachtlijst geplaatst.');
    $this->assertDatabaseHas('lesson_registrations', [
        'lesson_id' => $lesson->id,
        'user_id' => $member->id,
        'status' => 'waitlisted',
    ]);
});

it('reactivates a cancelled registration and resets its timestamps', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-11 09:00:00',
        'max_participants' => 1,
    ]);
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'cancelled',
        'registered_at' => now()->subDays(2),
        'canceled_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($member)
        ->post(route('lessons.register', $lesson));

    $response->assertSessionHas('status', 'Je bent ingeschreven voor de les.');

    $registration->refresh();
    expect($registration->status)->toBe('enrolled')
        ->and($registration->registered_at->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($registration->canceled_at)->toBeNull();
    $this->assertDatabaseCount('lesson_registrations', 1);
});

it('cancels an enrolled member exactly two hours before and promotes the oldest waiter', function () {
    $this->travelTo('2026-10-10 10:00:00');
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
        'max_participants' => 1,
    ]);
    $enrolledMember = User::factory()->create();
    $oldestWaitlistedMember = User::factory()->create();
    $newerWaitlistedMember = User::factory()->create();

    $enrolled = $lesson->registrations()->create([
        'user_id' => $enrolledMember->id,
        'status' => 'enrolled',
        'registered_at' => now()->subHours(2),
    ]);
    $oldestWaitlisted = $lesson->registrations()->create([
        'user_id' => $oldestWaitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(10),
    ]);
    $newerWaitlisted = $lesson->registrations()->create([
        'user_id' => $newerWaitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(5),
    ]);

    $response = $this->actingAs($enrolledMember)
        ->delete(route('registrations.cancel', $enrolled));

    $response->assertRedirectToRoute('registrations.index');
    expect($enrolled->fresh()->status)->toBe('cancelled')
        ->and($enrolled->fresh()->canceled_at->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($oldestWaitlisted->fresh()->status)->toBe('enrolled')
        ->and($newerWaitlisted->fresh()->status)->toBe('waitlisted');
});

it('does not cancel or promote when the cancellation deadline has passed', function () {
    $this->travelTo('2026-10-10 10:00:01');
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
        'max_participants' => 1,
    ]);
    $member = User::factory()->create();
    $waitlistedMember = User::factory()->create();
    $enrolled = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now()->subHour(),
    ]);
    $waitlisted = $lesson->registrations()->create([
        'user_id' => $waitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(30),
    ]);

    $response = $this->actingAs($member)
        ->from(route('registrations.index'))
        ->delete(route('registrations.cancel', $enrolled));

    $response->assertRedirectBackWithErrors(['registration']);
    expect($enrolled->fresh()->status)->toBe('enrolled')
        ->and($waitlisted->fresh()->status)->toBe('waitlisted');
});

it('does not promote another member when a waitlisted member cancels', function () {
    $this->travelTo('2026-10-10 10:00:00');
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
        'max_participants' => 1,
    ]);
    $member = User::factory()->create();
    $enrolledMember = User::factory()->create();
    $nextMember = User::factory()->create();
    $waitlisted = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(10),
    ]);
    $enrolled = $lesson->registrations()->create([
        'user_id' => $enrolledMember->id,
        'status' => 'enrolled',
        'registered_at' => now()->subMinutes(20),
    ]);
    $nextWaitlisted = $lesson->registrations()->create([
        'user_id' => $nextMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(5),
    ]);

    $response = $this->actingAs($member)
        ->delete(route('registrations.cancel', $waitlisted));

    $response->assertRedirectToRoute('registrations.index');
    expect($waitlisted->fresh()->status)->toBe('cancelled')
        ->and($enrolled->fresh()->status)->toBe('enrolled')
        ->and($nextWaitlisted->fresh()->status)->toBe('waitlisted');
});

it('does not let a member cancel another member’s registration', function () {
    $member = User::factory()->create();
    $otherMember = User::factory()->create();
    $registration = LessonRegistration::factory()->create([
        'user_id' => $otherMember->id,
    ]);

    $response = $this->actingAs($member)
        ->delete(route('registrations.cancel', $registration));

    $response->assertNotFound();
    expect($registration->fresh()->status)->toBe('enrolled');
});

it('allows only an admin to cancel for a member after the self-cancellation deadline and promotes the next waiter', function () {
    $this->travelTo('2026-10-10 10:00:01');
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $waitlistedMember = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
        'max_participants' => 1,
    ]);
    $enrolled = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now()->subHour(),
    ]);
    $waitlisted = $lesson->registrations()->create([
        'user_id' => $waitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->subMinutes(30),
    ]);

    $this->actingAs(User::factory()->trainer()->create())
        ->delete(route('members.registrations.cancel', $enrolled))
        ->assertForbidden();

    $this->actingAs($admin)
        ->delete(route('members.registrations.cancel', $enrolled))
        ->assertRedirectToRoute('participants.index', ['participant' => $member->id])
        ->assertSessionHas('status', 'De afmelding namens het lid is geregistreerd.');

    expect($enrolled->fresh()->status)->toBe('cancelled')
        ->and($enrolled->fresh()->canceled_at->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($waitlisted->fresh()->status)->toBe('enrolled');
});

it('does not allow staff to cancel for a member after the lesson has started', function () {
    $this->travelTo('2026-10-10 12:00:00');
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 12:00:00',
    ]);
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now()->subHour(),
    ]);

    $this->actingAs($admin)
        ->from(route('participants.index', ['participant' => $member->id]))
        ->delete(route('members.registrations.cancel', $registration))
        ->assertRedirectBackWithErrors(['registration']);

    expect($registration->fresh()->status)->toBe('enrolled');
});
