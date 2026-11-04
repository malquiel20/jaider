<?php

use App\Models\Contact;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create([
        'first_name' => 'Ana',
        'last_name' => 'Pérez',
        'email' => 'ana@contactos.test',
        'phone_number' => '3001112233',
    ]);
});

// GET /api/v1/auth/me

it('muestra el perfil del usuario autenticado', function () {
    Contact::factory(3)->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'ana@contactos.test')
        ->assertJsonPath('data.full_name', 'Ana Pérez')
        ->assertJsonPath('data.phone_number', '3001112233')
        ->assertJsonPath('data.contacts_count', 3)
        ->assertJsonMissingPath('data.password');
});

it('pide iniciar sesión para ver el perfil', function () {
    $this->getJson('/api/v1/auth/me')
        ->assertUnauthorized()
        ->assertJsonPath('login_url', route('auth.login'));
});

it('responde 401 en JSON aunque falte la cabecera Accept', function () {
    $this->get('/api/v1/auth/me')
        ->assertUnauthorized()
        ->assertHeader('Content-Type', 'application/json');
});

// PUT /api/v1/auth/me

it('actualiza el perfil del usuario', function () {
    Sanctum::actingAs($this->user);

    $this->putJson('/api/v1/auth/me', [
        'first_name' => 'Ana María',
        'phone_number' => '3009998877',
    ])
        ->assertOk()
        ->assertJsonPath('data.first_name', 'Ana María')
        ->assertJsonPath('data.phone_number', '3009998877');

    expect($this->user->fresh()->first_name)->toBe('Ana María');
});

it('permite dejar el mismo correo y teléfono propios', function () {
    Sanctum::actingAs($this->user);

    $this->putJson('/api/v1/auth/me', [
        'email' => 'ana@contactos.test',
        'phone_number' => '3001112233',
    ])->assertOk();
});

it('no deja usar el correo de otro usuario en el perfil', function () {
    User::factory()->create(['email' => 'luis@contactos.test']);
    Sanctum::actingAs($this->user);

    $this->putJson('/api/v1/auth/me', ['email' => 'luis@contactos.test'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('no deja usar el teléfono de otro usuario en el perfil', function () {
    User::factory()->create(['phone_number' => '3205554433']);
    Sanctum::actingAs($this->user);

    $this->putJson('/api/v1/auth/me', ['phone_number' => '3205554433'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['phone_number']);
});

it('pide iniciar sesión para actualizar el perfil', function () {
    $this->putJson('/api/v1/auth/me', ['first_name' => 'X'])
        ->assertUnauthorized();
});
