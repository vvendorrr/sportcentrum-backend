<?php

use App\Models\Lesson;
use App\Models\User;

it('shows the account page only to authenticated users', function () {
    $member = User::factory()->create();

    $this->get(route('account.show'))
        ->assertRedirect(route('home'));

    $this->actingAs($member)
        ->get(route('account.show'))
        ->assertOk()
        ->assertSeeText('Accountinstellingen')
        ->assertSeeText('Account verwijderen')
        ->assertSee(route('account.destroy'));
});

it('requires the current password before deleting an account', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->from(route('account.show'))
        ->delete(route('account.destroy'), ['password' => 'incorrect-password'])
        ->assertRedirect(route('account.show'))
        ->assertSessionHasErrors([
            'password' => 'Het opgegeven wachtwoord is onjuist.',
        ]);

    $this->assertAuthenticatedAs($member);
    $this->assertDatabaseHas('users', ['id' => $member->id]);
});

it('deletes the account, frees enrolled places, promotes waitlisted members, and keeps taught lessons', function () {
    $member = User::factory()->admin()->create();
    $waitlistedMember = User::factory()->create();
    $lesson = Lesson::factory()->create([
        'instructor_id' => $member->id,
        'max_participants' => 1,
    ]);

    $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);
    $lesson->registrations()->create([
        'user_id' => $waitlistedMember->id,
        'status' => 'waitlisted',
        'registered_at' => now()->addSecond(),
    ]);

    $this->actingAs($member)
        ->delete(route('account.destroy'), ['password' => 'password'])
        ->assertRedirect(route('home'))
        ->assertSessionHas('status', 'Je account is verwijderd.');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['id' => $member->id]);
    $this->assertDatabaseMissing('lesson_registrations', ['user_id' => $member->id]);
    $this->assertDatabaseHas('lesson_registrations', [
        'user_id' => $waitlistedMember->id,
        'status' => 'enrolled',
    ]);
    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'instructor_id' => null,
    ]);

    $this->get(route('lessons.show', $lesson))
        ->assertOk()
        ->assertSeeText('Voormalige trainer');
});
