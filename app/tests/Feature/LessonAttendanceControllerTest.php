<?php

use App\Models\Lesson;
use App\Models\User;

it('shows enrolled participant names only to admins', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['name' => 'Attending Member']);
    $waitlistedMember = User::factory()->create(['name' => 'Waitlisted Member']);
    $lesson = Lesson::factory()->create(['sport' => 'Evening yoga']);
    $lesson->registrations()->createMany([
        [
            'user_id' => $member->id,
            'status' => 'enrolled',
            'registered_at' => now(),
        ],
        [
            'user_id' => $waitlistedMember->id,
            'status' => 'waitlisted',
            'registered_at' => now(),
        ],
    ]);

    $this->get(route('lessons.participants', $lesson))
        ->assertRedirect(route('home'));

    $this->actingAs($admin)
        ->get(route('lessons.participants', $lesson))
        ->assertOk()
        ->assertSeeText('Attending Member')
        ->assertDontSeeText('Waitlisted Member');

    $this->actingAs($member)
        ->get(route('lessons.participants', $lesson))
        ->assertForbidden();
});

it('allows the assigned trainer to view participants and record attendance for their lesson', function () {
    $this->travelTo('2026-10-10 10:00:00');
    $trainer = User::factory()->trainer()->create();
    $otherTrainer = User::factory()->trainer()->create();
    $member = User::factory()->create(['name' => 'Assigned Member']);
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'starts_at' => '2026-10-10 09:00:00',
        'duration_minutes' => 60,
    ]);
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $this->actingAs($trainer)
        ->get(route('lessons.participants', $lesson))
        ->assertOk()
        ->assertSeeText('Assigned Member');

    $this->actingAs($trainer)
        ->patch(route('lessons.attendance.update', $lesson), [
            'attendance' => [['registration_id' => $registration->id, 'status' => 'attended']],
        ])
        ->assertRedirectToRoute('lessons.participants', $lesson);

    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $registration->id,
        'attendance' => 'attended',
    ]);

    $this->actingAs($otherTrainer)
        ->get(route('lessons.participants', $lesson))
        ->assertForbidden();
});

it('allows an admin to record, correct, and clear attendance after a lesson ends', function () {
    $this->travelTo('2026-10-10 10:00:00');
    $admin = User::factory()->admin()->create();
    $presentMember = User::factory()->create();
    $absentMember = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 09:00:00',
        'duration_minutes' => 60,
    ]);
    $presentRegistration = $lesson->registrations()->create([
        'user_id' => $presentMember->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $absentRegistration = $lesson->registrations()->create([
        'user_id' => $absentMember->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $attendance = [
        ['registration_id' => $presentRegistration->id, 'status' => 'attended'],
        ['registration_id' => $absentRegistration->id, 'status' => 'absent'],
    ];

    $this->actingAs($admin)
        ->patch(route('lessons.attendance.update', $lesson), ['attendance' => $attendance])
        ->assertRedirectToRoute('lessons.participants', $lesson)
        ->assertSessionHas('status', 'De aanwezigheid is bijgewerkt.');

    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $presentRegistration->id,
        'attendance' => 'attended',
    ]);
    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $absentRegistration->id,
        'attendance' => 'absent',
    ]);

    $attendance[0]['status'] = '';
    $attendance[1]['status'] = 'attended';

    $this->actingAs($admin)
        ->patch(route('lessons.attendance.update', $lesson), ['attendance' => $attendance])
        ->assertRedirectToRoute('lessons.participants', $lesson);

    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $presentRegistration->id,
        'attendance' => null,
    ]);
    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $absentRegistration->id,
        'attendance' => 'attended',
    ]);
});

it('does not allow attendance changes before the lesson ends', function () {
    $this->travelTo('2026-10-10 09:59:59');
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 09:00:00',
        'duration_minutes' => 60,
    ]);
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('lessons.attendance.update', $lesson), [
            'attendance' => [['registration_id' => $registration->id, 'status' => 'absent']],
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $registration->id,
        'attendance' => null,
    ]);
});

it('rejects waitlisted registrations and registrations from other lessons', function () {
    $this->travelTo('2026-10-10 12:00:00');
    $admin = User::factory()->admin()->create();
    $waitlistedMember = User::factory()->create();
    $enrolledMember = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 09:00:00',
        'duration_minutes' => 60,
    ]);
    $otherLesson = Lesson::factory()->create([
        'starts_at' => '2026-10-10 09:00:00',
        'duration_minutes' => 60,
    ]);
    $waitlistedRegistration = $lesson->registrations()->create([
        'user_id' => $waitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now(),
    ]);
    $otherRegistration = $otherLesson->registrations()->create([
        'user_id' => $enrolledMember->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    foreach ([$waitlistedRegistration, $otherRegistration] as $registration) {
        $this->actingAs($admin)
            ->from(route('lessons.participants', $lesson))
            ->patch(route('lessons.attendance.update', $lesson), [
                'attendance' => [['registration_id' => $registration->id, 'status' => 'absent']],
            ])
            ->assertRedirectBackWithErrors();

        $this->assertDatabaseHas('lesson_registrations', [
            'id' => $registration->id,
            'attendance' => null,
        ]);
    }
});

it('shows a member’s missed lesson count from absence records', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['name' => 'Absent Member']);
    $absentLesson = Lesson::factory()->create();
    $attendedLesson = Lesson::factory()->create();
    $unmarkedLesson = Lesson::factory()->create();

    $absentLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
        'attendance' => 'absent',
    ]);
    $attendedLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
        'attendance' => 'attended',
    ]);
    $unmarkedLesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
        'attendance' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('participants.index', ['participant' => $member->id]))
        ->assertOk()
        ->assertSeeText('Gemiste lessen')
        ->assertSeeText('1');
});
