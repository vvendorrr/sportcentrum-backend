<?php

use App\Models\Lesson;
use App\Models\User;

it('shows only the authenticated member’s future active registrations', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $member = User::factory()->create();
    $otherMember = User::factory()->create();

    $ownEnrolledLesson = Lesson::factory()->create([
        'sport' => 'My enrolled class',
        'starts_at' => '2026-10-11 09:00:00',
    ]);
    $ownWaitlistedLesson = Lesson::factory()->create([
        'sport' => 'My waitlisted class',
        'starts_at' => '2026-10-12 09:00:00',
    ]);
    $otherLesson = Lesson::factory()->create([
        'sport' => 'Another member’s class',
        'starts_at' => '2026-10-13 09:00:00',
    ]);
    $pastLesson = Lesson::factory()->create([
        'sport' => 'Past class',
        'starts_at' => '2026-10-09 09:00:00',
    ]);
    $cancelledLesson = Lesson::factory()->create([
        'sport' => 'Cancelled class',
        'starts_at' => '2026-10-14 09:00:00',
    ]);

    $ownEnrolledLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $ownWaitlistedLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'waitlisted',
        'registered_at' => now(),
    ]);
    $otherLesson->registrations()->create([
        'user_id' => $otherMember->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $pastLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now()->subDay(),
    ]);
    $cancelledLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'cancelled',
        'registered_at' => now(),
        'canceled_at' => now(),
    ]);

    $response = $this->actingAs($member)->get(route('registrations.index'));

    $response
        ->assertOk()
        ->assertSeeText('My enrolled class')
        ->assertSeeText('My waitlisted class')
        ->assertDontSeeText('Another member’s class')
        ->assertDontSeeText('Past class')
        ->assertDontSeeText('Cancelled class');
});
