<?php

use App\Models\Lesson;
use App\Models\User;

it('allows an admin to disable a member while preserving their account and registrations', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create([
        'name' => 'Sam Member',
        'email' => 'sam@example.com',
    ]);
    $lesson = Lesson::factory()->create();
    $registration = $lesson->registrations()->create([
        'user_id' => $member->id,
        'status' => 'enrolled',
        'registered_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('members.deactivate', $member))
        ->assertRedirectToRoute('participants.index', ['participant' => $member->id])
        ->assertSessionHas('status', 'Het ledenaccount is uitgeschakeld.');

    $this->assertDatabaseHas('users', [
        'id' => $member->id,
        'name' => 'Sam Member',
        'email' => 'sam@example.com',
        'is_active' => false,
    ]);
    $this->assertDatabaseHas('lesson_registrations', [
        'id' => $registration->id,
        'user_id' => $member->id,
        'status' => 'enrolled',
    ]);
});

it('does not allow a member to disable another member account', function () {
    $actor = User::factory()->create();
    $member = User::factory()->create();

    $this->actingAs($actor)
        ->patch(route('members.deactivate', $member))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $member->id, 'is_active' => true]);
});

it('does not allow an admin to disable a trainer account through the member action', function () {
    $admin = User::factory()->admin()->create();
    $trainer = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('members.deactivate', $trainer))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $trainer->id, 'is_active' => true]);
});

it('shows member account status and only offers the disable action while active', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('participants.index', ['participant' => $member->id]))
        ->assertOk()
        ->assertSeeText('Accountstatus')
        ->assertSeeText('Actief')
        ->assertSeeText('Ledenaccount uitschakelen')
        ->assertSee(route('members.deactivate', $member), false);

    $member->update(['is_active' => false]);

    $this->actingAs($admin)
        ->get(route('participants.index', ['participant' => $member->id]))
        ->assertOk()
        ->assertSeeText('Uitgeschakeld')
        ->assertDontSeeText('Ledenaccount uitschakelen');
});

it('logs out a member whose account is disabled during an existing session', function () {
    $member = User::factory()->inactive()->create();

    $this->actingAs($member)
        ->get(route('account.show'))
        ->assertRedirectToRoute('home')
        ->assertSessionHas('status', 'Je account is uitgeschakeld.');

    $this->assertGuest();
});
