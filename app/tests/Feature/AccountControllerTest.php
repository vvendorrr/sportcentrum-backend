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
        ->assertSee(route('account.update'))
        ->assertSee(route('account.email.update'))
        ->assertSee(route('account.password.update'))
        ->assertSeeText('Account verwijderen')
        ->assertSee(route('account.destroy'));
});

it('updates the name and phone number of the authenticated account', function () {
    $member = User::factory()->create(['name' => 'N. Pieter', 'phone_number' => '0687654321']);

    $this->actingAs($member)
        ->patch(route('account.update'), [
            'first_name' => 'Merel',
            'last_name' => 'de Vries',
            'phone_number' => '0612345678',
        ])
        ->assertRedirect(route('account.show'))
        ->assertSessionHas('status', 'Je accountgegevens zijn bijgewerkt.');

    $this->assertDatabaseHas('users', [
        'id' => $member->id,
        'name' => 'Merel de Vries',
        'phone_number' => '0612345678',
    ]);
});

it('clears the phone number when it is submitted empty', function () {
    $member = User::factory()->create(['phone_number' => '0687654321']);

    $this->actingAs($member)
        ->patch(route('account.update'), [
            'first_name' => 'Merel',
            'last_name' => 'de Vries',
        ])
        ->assertRedirect(route('account.show'));

    $this->assertDatabaseHas('users', [
        'id' => $member->id,
        'phone_number' => null,
    ]);
});

it('rejects invalid profile input', function () {
    $member = User::factory()->create(['name' => 'Merel de Vries']);

    $this->actingAs($member)
        ->patch(route('account.update'), [
            'first_name' => '',
            'last_name' => str_repeat('a', 256),
            'phone_number' => str_repeat('9', 31),
        ])
        ->assertRedirect()
        ->assertSessionHasErrors([
            'first_name' => 'Vul je voornaam in.',
            'last_name' => 'Je achternaam mag maximaal 255 tekens bevatten.',
            'phone_number' => 'Je telefoonnummer mag maximaal 30 tekens bevatten.',
        ]);

    $this->assertDatabaseHas('users', ['id' => $member->id, 'name' => 'Merel de Vries']);
});

it('updates the email address of the authenticated account', function () {
    $member = User::factory()->create(['email' => 'merel@example.com']);

    $this->actingAs($member)
        ->patch(route('account.email.update'), [
            'email' => 'MEREL.deVries@Example.nl',
        ])
        ->assertRedirect(route('account.show', ['tab' => 'security']))
        ->assertSessionHas('status', 'Je e-mailadres is bijgewerkt.');

    $this->assertDatabaseHas('users', ['id' => $member->id, 'email' => 'merel.devries@example.nl']);
});

it('rejects an email address that is already in use', function () {
    $member = User::factory()->create(['email' => 'merel@example.com']);
    $other = User::factory()->create(['email' => 'pieter@example.com']);

    $this->actingAs($member)
        ->patch(route('account.email.update'), ['email' => 'Pieter@example.com'])
        ->assertRedirect()
        ->assertSessionHasErrors(['email' => 'Er bestaat al een account met dit e-mailadres.']);

    $this->assertDatabaseHas('users', ['id' => $member->id, 'email' => 'merel@example.com']);
    $this->assertDatabaseHas('users', ['id' => $other->id, 'email' => 'pieter@example.com']);
});

it('changes the password after verifying the current password', function () {
    $member = User::factory()->create();
    $currentHash = $member->password_hash;

    $this->actingAs($member)
        ->patch(route('account.password.update'), [
            'current_password' => 'password',
            'new_password' => 'een-sterk-nieuw-wachtwoord',
            'new_password_confirmation' => 'een-sterk-nieuw-wachtwoord',
        ])
        ->assertRedirect(route('account.show', ['tab' => 'security']))
        ->assertSessionHas('status', 'Je wachtwoord is gewijzigd.');

    $this->assertDatabaseMissing('users', ['id' => $member->id, 'password_hash' => $currentHash]);
});

it('requires the correct current password to change the password', function () {
    $member = User::factory()->create();
    $currentHash = $member->password_hash;

    $this->actingAs($member)
        ->patch(route('account.password.update'), [
            'current_password' => 'incorrect-password',
            'new_password' => 'een-sterk-nieuw-wachtwoord',
            'new_password_confirmation' => 'een-sterk-nieuw-wachtwoord',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors(['current_password' => 'Het opgegeven wachtwoord is onjuist.']);

    $this->assertDatabaseHas('users', ['id' => $member->id, 'password_hash' => $currentHash]);
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
    $member = User::factory()->trainer()->create();
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
