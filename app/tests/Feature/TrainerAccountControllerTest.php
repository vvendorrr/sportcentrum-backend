<?php

use App\Models\Lesson;
use App\Models\User;

it('allows an admin to update a trainer account', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create([
        'name' => 'Oude Naam',
        'email' => 'oud@example.com',
        'phone_number' => '0612345678',
    ]);

    $this->actingAs($admin)
        ->patch(route('trainers.update', $trainer), [
            'name' => 'Nieuwe Naam',
            'email' => 'nieuw@example.com',
            'phone_number' => '0687654321',
            'is_active' => '1',
        ])
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id])
        ->assertSessionHas('status', 'Het traineraccount is bijgewerkt.');

    $this->assertDatabaseHas('users', [
        'id' => $trainer->id,
        'name' => 'Nieuwe Naam',
        'email' => 'nieuw@example.com',
        'phone_number' => '0687654321',
        'is_active' => true,
    ]);
});

it('allows an admin to deactivate a trainer account', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('trainers.update', $trainer), [
            'name' => $trainer->name,
            'email' => $trainer->email,
            'phone_number' => $trainer->phone_number,
            'is_active' => '0',
        ])
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id]);

    $this->assertDatabaseHas('users', ['id' => $trainer->id, 'is_active' => false]);
});

it('rejects a duplicate email when updating a trainer account', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->admin()->create(['email' => 'bezocht@example.com']);
    $trainer = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('trainers.update', $trainer), [
            'name' => $trainer->name,
            'email' => 'bezocht@example.com',
            'phone_number' => $trainer->phone_number,
            'is_active' => '1',
        ])
        ->assertRedirectBackWithErrors([
            'email' => 'Er bestaat al een account met dit e-mailadres.',
        ]);
});

it('does not allow a member to update a trainer account', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->admin()->create();

    $this->actingAs($member)
        ->patch(route('trainers.update', $trainer), [
            'name' => 'Gewijzigd',
            'email' => 'gewijzigd@example.com',
            'is_active' => '1',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $trainer->id, 'name' => $trainer->name]);
});

it('does not allow an admin to update a member account through the trainer action', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->patch(route('trainers.update', $member), [
            'name' => 'Gewijzigd',
            'email' => 'gewijzigd@example.com',
            'is_active' => '1',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $member->id, 'name' => $member->name]);
});

it('allows an admin to assign upcoming lessons to a trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();
    $otherTrainer = User::factory()->admin()->create();
    $orphanedLesson = Lesson::factory()->create([
        'instructor_id' => null,
        'starts_at' => '2026-10-11 09:00:00',
    ]);
    $reassignedLesson = Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'starts_at' => '2026-10-12 10:00:00',
    ]);
    $ownLesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'starts_at' => '2026-10-13 10:00:00',
    ]);

    $this->actingAs($admin)
        ->post(route('trainers.lessons.assign', $trainer), [
            'lessons' => [$orphanedLesson->id, $reassignedLesson->id],
        ])
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id])
        ->assertSessionHas('status', 'De geselecteerde lessen zijn toegewezen aan deze trainer.');

    $this->assertDatabaseHas('lessons', ['id' => $orphanedLesson->id, 'instructor_id' => $trainer->id]);
    $this->assertDatabaseHas('lessons', ['id' => $reassignedLesson->id, 'instructor_id' => $trainer->id]);
    $this->assertDatabaseHas('lessons', ['id' => $ownLesson->id, 'instructor_id' => $trainer->id]);
});

it('rejects assigning past lessons to a trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();
    $otherTrainer = User::factory()->admin()->create();
    $pastLesson = Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'starts_at' => '2026-10-09 09:00:00',
    ]);

    $this->actingAs($admin)
        ->post(route('trainers.lessons.assign', $trainer), [
            'lessons' => [$pastLesson->id],
        ])
        ->assertRedirectBackWithErrors()
        ->assertSessionHasErrors('lessons.0');

    $this->assertDatabaseHas('lessons', ['id' => $pastLesson->id, 'instructor_id' => $otherTrainer->id]);
});

it('rejects an empty lesson selection when assigning lessons', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('trainers.lessons.assign', $trainer), [
            'lessons' => [],
        ])
        ->assertRedirectBackWithErrors([
            'lessons' => 'Selecteer minimaal één les.',
        ]);
});

it('does not allow a member to assign lessons to a trainer', function () {
    $member = User::factory()->create();
    $trainer = User::factory()->admin()->create();
    $lesson = Lesson::factory()->create(['starts_at' => now()->addDay()]);

    $this->actingAs($member)
        ->post(route('trainers.lessons.assign', $trainer), [
            'lessons' => [$lesson->id],
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id, 'instructor_id' => $trainer->id]);
});

it('allows an admin to detach an upcoming lesson from a trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'starts_at' => '2026-10-11 09:00:00',
    ]);

    $this->actingAs($admin)
        ->delete(route('trainers.lessons.detach', [$trainer, $lesson]))
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id])
        ->assertSessionHas('status', 'De toewijzing van deze les is opgeheven.');

    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'instructor_id' => null]);
});

it('does not allow detaching a lesson that is not taught by the trainer', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();
    $otherTrainer = User::factory()->admin()->create();
    $lesson = Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'starts_at' => now()->addDay(),
    ]);

    $this->actingAs($admin)
        ->delete(route('trainers.lessons.detach', [$trainer, $lesson]))
        ->assertNotFound();

    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'instructor_id' => $otherTrainer->id]);
});

it('renders the trainer edit and assignment forms for admins', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create(['name' => 'D. Jones']);
    $assignableLesson = Lesson::factory()->create([
        'instructor_id' => null,
        'sport' => 'Assign yoga',
        'starts_at' => '2026-10-11 09:00:00',
    ]);
    $assignedLesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'sport' => 'Owned yoga',
        'starts_at' => '2026-10-12 09:00:00',
    ]);

    $this->actingAs($admin)
        ->get(route('trainers.index', ['trainer' => $trainer->id]))
        ->assertOk()
        ->assertSeeText('Traineraccount bewerken')
        ->assertSeeText('Lessen toewijzen')
        ->assertSeeText('Assign yoga')
        ->assertSeeText('Owned yoga')
        ->assertSee(route('trainers.update', $trainer), false)
        ->assertSee(route('trainers.lessons.assign', $trainer), false)
        ->assertSee(route('trainers.lessons.detach', [$trainer, $assignedLesson]), false)
        ->assertDontSee(route('trainers.lessons.detach', [$trainer, $assignableLesson]), false);
});

it('hides the trainer management UI until a trainer is selected', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->admin()->create(['name' => 'D. Jones']);

    $this->actingAs($admin)
        ->get(route('trainers.index'))
        ->assertOk()
        ->assertSeeText('D. Jones')
        ->assertDontSeeText('Traineraccount bewerken')
        ->assertDontSeeText('Lessen toewijzen')
        ->assertSeeText('Kies een trainer uit de lijst om de gegevens te bekijken en te bewerken.');
});

it('does not allow assigning lessons to an inactive trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->inactive()->create();
    $otherTrainer = User::factory()->admin()->create();
    $lesson = Lesson::factory()->create([
        'instructor_id' => $otherTrainer->id,
        'starts_at' => '2026-10-11 09:00:00',
    ]);

    $this->actingAs($admin)
        ->post(route('trainers.lessons.assign', $trainer), [
            'lessons' => [$lesson->id],
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'instructor_id' => $otherTrainer->id]);
});

it('does not show the assignment form for an inactive trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->inactive()->create(['name' => 'I. Trainer']);
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'starts_at' => '2026-10-11 09:00:00',
    ]);

    $this->actingAs($admin)
        ->get(route('trainers.index', ['trainer' => $trainer->id]))
        ->assertOk()
        ->assertSeeText('Traineraccount bewerken')
        ->assertDontSeeText('Lessen toewijzen')
        ->assertSeeText('Dit traineraccount is inactief. Activeer het account om weer lessen toe te wijzen.')
        ->assertSee(route('trainers.lessons.detach', [$trainer, $lesson]), false);
});

it('allows an admin to detach a lesson from an inactive trainer', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->inactive()->create();
    $lesson = Lesson::factory()->create([
        'instructor_id' => $trainer->id,
        'starts_at' => '2026-10-11 09:00:00',
    ]);

    $this->actingAs($admin)
        ->delete(route('trainers.lessons.detach', [$trainer, $lesson]))
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id])
        ->assertSessionHas('status', 'De toewijzing van deze les is opgeheven.');

    $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'instructor_id' => null]);
});

it('keeps the trainer editor collapsed until the trainer name is pressed', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('trainers.index', ['trainer' => $trainer->id]))
        ->assertOk()
        ->assertSee('id="trainer-edit-toggle"', false)
        ->assertSee('aria-expanded="false"', false)
        ->assertSee('id="trainer-edit-section"', false)
        ->assertSeeText('Traineraccount bewerken');
});

it('reveals the trainer editor when saving fails validation', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();
    User::factory()->admin()->create(['email' => 'bezocht@example.com']);

    $this->actingAs($admin)
        ->from(route('trainers.index', ['trainer' => $trainer->id]))
        ->patch(route('trainers.update', $trainer), [
            'name' => $trainer->name,
            'email' => 'bezocht@example.com',
            'is_active' => '1',
        ])
        ->assertRedirectBackWithErrors();

    $this->get(route('trainers.index', ['trainer' => $trainer->id]))
        ->assertOk()
        ->assertSee('aria-expanded="true"', false);
});

it('renders the trainer creation form for admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('trainers.create'))
        ->assertOk()
        ->assertSeeText('Traineraccount aanmaken')
        ->assertSee(route('trainers.store'), false)
        ->assertSee(route('trainers.index'), false);
});

it('does not allow a member to open the trainer creation form', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get(route('trainers.create'))
        ->assertForbidden();
});

it('allows an admin to create a trainer account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('trainers.store'), [
            'name' => 'N. Trainer',
            'email' => 'N.Trainer@Example.com',
            'phone_number' => '0612345678',
            'password' => 'ZekerWachtwoord123',
            'password_confirmation' => 'ZekerWachtwoord123',
            'is_active' => '1',
        ]);

    $trainer = User::query()->where('email', 'n.trainer@example.com')->firstOrFail();

    $response
        ->assertRedirectToRoute('trainers.index', ['trainer' => $trainer->id])
        ->assertSessionHas('status', 'Het traineraccount is aangemaakt.');

    $this->assertDatabaseHas('users', [
        'id' => $trainer->id,
        'name' => 'N. Trainer',
        'email' => 'n.trainer@example.com',
        'phone_number' => '0612345678',
        'role' => 'admin',
        'is_active' => true,
    ]);
});

it('allows an admin to create an inactive trainer without a phone number', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('trainers.store'), [
            'name' => 'I. Trainer',
            'email' => 'i.trainer@example.com',
            'password' => 'ZekerWachtwoord123',
            'password_confirmation' => 'ZekerWachtwoord123',
            'is_active' => '0',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'i.trainer@example.com',
        'phone_number' => null,
        'is_active' => false,
    ]);
});

it('rejects a duplicate email when creating a trainer account', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->admin()->create(['email' => 'bezocht@example.com']);

    $this->actingAs($admin)
        ->post(route('trainers.store'), [
            'name' => 'N. Trainer',
            'email' => 'bezocht@example.com',
            'password' => 'ZekerWachtwoord123',
            'password_confirmation' => 'ZekerWachtwoord123',
            'is_active' => '1',
        ])
        ->assertRedirectBackWithErrors([
            'email' => 'Er bestaat al een account met dit e-mailadres.',
        ]);
});

it('rejects mismatched passwords when creating a trainer account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('trainers.store'), [
            'name' => 'N. Trainer',
            'email' => 'n.trainer@example.com',
            'password' => 'ZekerWachtwoord123',
            'password_confirmation' => 'AndersWachtwoord123',
            'is_active' => '1',
        ])
        ->assertRedirectBackWithErrors([
            'password' => 'De wachtwoorden komen niet overeen.',
        ]);
});

it('rejects a too short password when creating a trainer account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('trainers.store'), [
            'name' => 'N. Trainer',
            'email' => 'n.trainer@example.com',
            'password' => 'kort',
            'password_confirmation' => 'kort',
            'is_active' => '1',
        ])
        ->assertRedirectBackWithErrors([
            'password' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
        ]);
});

it('does not allow a member to create a trainer account', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->post(route('trainers.store'), [
            'name' => 'N. Trainer',
            'email' => 'n.trainer@example.com',
            'password' => 'ZekerWachtwoord123',
            'password_confirmation' => 'ZekerWachtwoord123',
            'is_active' => '1',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'n.trainer@example.com']);
});

it('shows the trainer creation button for admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('trainers.index'))
        ->assertOk()
        ->assertSee(route('trainers.create'), false);
});

it('does not render trainer management forms in the participants directory', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('participants.index', ['participant' => $member->id]))
        ->assertOk()
        ->assertDontSeeText('Traineraccount bewerken')
        ->assertDontSeeText('Lessen toewijzen')
        ->assertDontSee('trainer-edit-toggle', false)
        ->assertDontSee(route('trainers.create'), false);
});
