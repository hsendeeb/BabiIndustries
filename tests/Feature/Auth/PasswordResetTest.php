<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends a password reset link to an existing user', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/forgot-password', [
        'email' => $user->email,
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'message' => __('passwords.sent'),
        ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not reveal whether the user exists when requesting a password reset link', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/forgot-password', [
        'email' => 'missing@example.com',
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'message' => __('passwords.sent'),
        ]);

    Notification::assertNothingSent();
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create([
        'password' => 'old-password',
    ]);

    $token = Password::createToken($user);

    $response = $this->postJson('/api/v1/reset-password', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'message' => __('passwords.reset'),
        ]);

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects a password reset with an invalid token', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/reset-password', [
        'email' => $user->email,
        'token' => 'invalid-token',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'message' => __('passwords.token'),
        ]);
});
