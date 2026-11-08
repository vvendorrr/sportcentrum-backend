<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('renders the login form', function () {
    $response = $this->get(route('login'));

    $response
        ->assertOk()
        ->assertSeeText('Inloggen');
});

it('renders the registration form', function () {
    $response = $this->get(route('register'));

    $response
        ->assertOk()
        ->assertSeeText('Account aanmaken');
});

it('redirects authenticated users away from guest authentication pages', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertRedirect(route('home'));
});

it('registers visitors as active members and hashes their passwords', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Sam Member',
        'email' => 'sam@example.com',
        'phone_number' => '0612345678',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'role' => 'admin',
        'is_active' => false,
    ]);

    $response
        ->assertRedirectToRoute('login')
        ->assertSessionHas('status', 'Je account is aangemaakt. Je kunt nu inloggen.');

    $user = User::query()->where('email', 'sam@example.com')->firstOrFail();

    expect($user->role)->toBe('member')
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('secure-password', $user->password_hash))->toBeTrue();
});

it('rejects registration with an email address that is already in use', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Sam Member',
        'email' => 'EXISTING@example.com',
        'phone_number' => '0612345678',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ]);

    $response->assertRedirectBackWithErrors([
        'email' => 'Er bestaat al een account met dit e-mailadres.',
    ]);
    $this->assertDatabaseCount('users', 1);
});

it('authenticates an active member and redirects to the lesson schedule', function () {
    $user = User::factory()->create([
        'email' => 'sam@example.com',
        'password_hash' => 'secure-password',
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'sam@example.com',
        'password' => 'secure-password',
    ]);

    $response->assertRedirectToRoute('lessons.index');
    $this->assertAuthenticatedAs($user);
});

it('rejects incorrect credentials', function () {
    User::factory()->create([
        'email' => 'sam@example.com',
        'password_hash' => 'secure-password',
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'sam@example.com',
        'password' => 'incorrect-password',
    ]);

    $response->assertRedirectBackWithErrors([
        'email' => 'De combinatie van e-mailadres en wachtwoord is onjuist.',
    ]);
    $this->assertGuest();
});

it('does not authenticate inactive accounts', function () {
    User::factory()->inactive()->create([
        'email' => 'inactive@example.com',
        'password_hash' => 'secure-password',
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'inactive@example.com',
        'password' => 'secure-password',
    ]);

    $response->assertRedirectBackWithErrors(['email']);
    $this->assertGuest();
});

it('logs out the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirectToRoute('home');
    $this->assertGuest();
});
