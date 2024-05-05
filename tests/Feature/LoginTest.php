<?php

use function Pest\Livewire\livewire;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Http;

test('can render login page', function () {
    livewire(Login::class)
        ->assertSuccessful();
});

test('can log in with valid credentials', function () {
    // Replace 'your-email' and 'your-password' with valid test credentials
    $email = 'foo@bar.com';
    $password = 'password';

    livewire(Login::class)
        ->set('email', $email)
        ->set('password', $password)
        ->call('authenticate')
        ->assertRedirect('/');
});

test('cannot log in with invalid credentials', function () {
    // Replace 'invalid-email' and 'invalid-password' with invalid test credentials
    $email = 'invalid-email';
    $password = 'invalid-password';

    livewire(Login::class)
        ->set('email', $email)
        ->set('password', $password)
        ->call('authenticate')
        ->assertHasErrors('email');
});
