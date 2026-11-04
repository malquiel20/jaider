<?php

use App\Models\User;

function loginData(array $cambios = []): array
{
    return array_merge([
        'email' => 'ana@contactos.test',
        'password' => 'secreta123',
        'device_name' => 'pruebas',
    ], $cambios);
}

function registerData(array $cambios = []): array
{
    return array_merge([
        'first_name' => 'Luis',
        'last_name' => 'Gómez',
        'email' => 'luis@contactos.test',
        'password' => 'secreta123',
        'password_confirmation' => 'secreta123',
        'phone_number' => '3105556677',
        'device_name' => 'pruebas',
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'ana@contactos.test',
        'password' => 'secreta123',
        'phone_number' => '3001112233',
    ]);
});

// POST /api/v1/auth/login

it('entrega un token con credenciales correctas', function () {
    $this->postJson('/api/v1/auth/login', loginData())
        ->assertOk()
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'first_name', 'email']])
        ->assertJsonPath('user.email', 'ana@contactos.test')
        ->assertJsonMissingPath('user.password');

    expect($this->user->tokens()->count())->toBe(1);
});

it('rechaza una contraseña incorrecta con 422', function () {
    $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('usa el mismo mensaje si el correo no existe', function () {
    $this->postJson('/api/v1/auth/login', loginData(['email' => 'nadie@contactos.test']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Las credenciales no son correctas.');
});

it('valida los campos obligatorios del login', function (string $campo) {
    $this->postJson('/api/v1/auth/login', loginData([$campo => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$campo]);
})->with(['email', 'password', 'device_name']);

it('bloquea el login al sexto intento fallido en un minuto', function () {
    foreach (range(1, 5) as $intento) {
        $this->postJson('/api/v1/auth/login', loginData(['password' => 'mala']))
            ->assertUnprocessable();
    }

    $this->postJson('/api/v1/auth/login', loginData(['password' => 'mala']))
        ->assertTooManyRequests()
        ->assertJsonPath('message', 'Demasiados intentos de inicio de sesión. Espera un minuto.');
});

// POST /api/v1/auth/register

it('registra un usuario nuevo y le entrega un token', function () {
    $this->postJson('/api/v1/auth/register', registerData())
        ->assertCreated()
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'first_name', 'email']])
        ->assertJsonPath('user.email', 'luis@contactos.test')
        ->assertJsonMissingPath('user.password');

    $user = User::where('email', 'luis@contactos.test')->first();
    expect($user)->not->toBeNull()
        ->and($user->password)->not->toBe('secreta123');
});

it('no registra un correo de usuario que ya está en uso', function () {
    $this->postJson('/api/v1/auth/register', registerData(['email' => 'ana@contactos.test']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Este correo ya está en uso por otro usuario.');
});

it('no registra un teléfono de usuario que ya está en uso', function () {
    $this->postJson('/api/v1/auth/register', registerData(['phone_number' => '3001112233']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.phone_number.0', 'Este número de teléfono ya está en uso por otro usuario.');
});

it('valida los campos obligatorios del registro', function (string $campo) {
    $this->postJson('/api/v1/auth/register', registerData([$campo => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$campo]);
})->with(['first_name', 'email', 'password', 'device_name']);

// POST /api/v1/auth/logout

it('cierra la sesión y revoca el token', function () {
    $token = $this->user->createToken('pruebas')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect($this->user->tokens()->count())->toBe(0);
});

it('no deja usar el token después del logout', function () {
    $token = $this->user->createToken('pruebas')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

    $this->app['auth']->forgetGuards();

    $this->withToken($token)->getJson('/api/v1/auth/me')
        ->assertUnauthorized();
});

it('pide token para cerrar sesión', function () {
    $this->postJson('/api/v1/auth/logout')
        ->assertUnauthorized();
});
