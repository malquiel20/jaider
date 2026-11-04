<?php

use App\Models\Contact;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function contactData(array $cambios = []): array
{
    return array_merge([
        'first_name' => 'Carlos',
        'last_name' => 'Ruiz',
        'phone_number' => '3114445566',
        'email' => 'carlos@correo.test',
        'address' => 'Calle 10 # 20-30',
        'avatar_url' => 'https://example.com/avatar.png',
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otro = User::factory()->create();
});

// Sin iniciar sesión

it('no deja entrar a los contactos sin iniciar sesión', function (string $metodo, string $url) {
    $contact = Contact::factory()->create();
    $url = str_replace('{id}', (string) $contact->id, $url);

    $this->json($metodo, $url, contactData())
        ->assertUnauthorized()
        ->assertJsonPath('message', 'No has iniciado sesión. Inicia sesión para continuar.')
        ->assertJsonPath('login_url', route('auth.login'));
})->with([
    'listar' => ['GET', '/api/v1/contacts'],
    'crear' => ['POST', '/api/v1/contacts'],
    'ver' => ['GET', '/api/v1/contacts/{id}'],
    'actualizar' => ['PUT', '/api/v1/contacts/{id}'],
    'eliminar' => ['DELETE', '/api/v1/contacts/{id}'],
]);

// GET /api/v1/contacts

it('lista solo los contactos del usuario autenticado', function () {
    Contact::factory(3)->for($this->user)->create();
    Contact::factory(2)->for($this->otro)->create();
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/contacts')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['id', 'first_name', 'phone_number', 'email', 'address']], 'links', 'meta']);
});

it('no lista los contactos eliminados', function () {
    Contact::factory(2)->for($this->user)->create();
    Contact::factory()->for($this->user)->create()->delete();
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/contacts')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

// POST /api/v1/contacts

it('crea un contacto para el usuario autenticado', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/contacts', contactData())
        ->assertCreated()
        ->assertJsonPath('data.first_name', 'Carlos')
        ->assertJsonPath('data.address', 'Calle 10 # 20-30');

    $this->assertDatabaseHas('contacts', [
        'email' => 'carlos@correo.test',
        'user_id' => $this->user->id,
    ]);
});

it('valida los campos obligatorios del contacto', function (string $campo) {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/contacts', contactData([$campo => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$campo]);
})->with(['first_name', 'phone_number', 'email', 'address']);

it('no deja crear un contacto con un correo ya en uso', function () {
    Contact::factory()->for($this->otro)->create(['email' => 'carlos@correo.test']);
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/contacts', contactData())
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Este correo ya está en uso por otro contacto.');
});

it('no deja crear un contacto con un teléfono ya en uso', function () {
    Contact::factory()->for($this->otro)->create(['phone_number' => '3114445566']);
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/contacts', contactData())
        ->assertUnprocessable()
        ->assertJsonPath('errors.phone_number.0', 'Este número de teléfono ya está en uso por otro contacto.');
});

it('rechaza un correo o teléfono con formato inválido', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/contacts', contactData(['email' => 'no-es-correo', 'phone_number' => 'abc']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'phone_number']);
});

// GET /api/v1/contacts/{id}

it('muestra el perfil de un contacto propio', function () {
    $contact = Contact::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->getJson("/api/v1/contacts/{$contact->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $contact->id)
        ->assertJsonPath('data.email', $contact->email)
        ->assertJsonPath('data.address', $contact->address);
});

it('no deja ver el contacto de otro usuario', function () {
    $contact = Contact::factory()->for($this->otro)->create();
    Sanctum::actingAs($this->user);

    $this->getJson("/api/v1/contacts/{$contact->id}")
        ->assertForbidden()
        ->assertJsonPath('message', 'No tienes permiso para acceder a este contacto.');
});

it('responde 404 en JSON si el contacto no existe', function () {
    Sanctum::actingAs($this->user);

    $this->get('/api/v1/contacts/99999')
        ->assertNotFound()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonPath('message', 'El recurso solicitado no existe.');
});

// PUT /api/v1/contacts/{id}

it('actualiza un contacto propio', function () {
    $contact = Contact::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->putJson("/api/v1/contacts/{$contact->id}", ['address' => 'Carrera 5 # 1-2'])
        ->assertOk()
        ->assertJsonPath('data.address', 'Carrera 5 # 1-2');

    expect($contact->fresh()->address)->toBe('Carrera 5 # 1-2');
});

it('permite conservar su propio correo y teléfono al actualizar', function () {
    $contact = Contact::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->putJson("/api/v1/contacts/{$contact->id}", [
        'email' => $contact->email,
        'phone_number' => $contact->phone_number,
    ])->assertOk();
});

it('no deja actualizar con el correo o teléfono de otro contacto', function () {
    $contact = Contact::factory()->for($this->user)->create();
    $existente = Contact::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->putJson("/api/v1/contacts/{$contact->id}", [
        'email' => $existente->email,
        'phone_number' => $existente->phone_number,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'phone_number']);
});

it('no deja actualizar el contacto de otro usuario', function () {
    $contact = Contact::factory()->for($this->otro)->create(['first_name' => 'Original']);
    Sanctum::actingAs($this->user);

    $this->putJson("/api/v1/contacts/{$contact->id}", ['first_name' => 'Hackeado'])
        ->assertForbidden();

    expect($contact->fresh()->first_name)->toBe('Original');
});

it('responde 404 al actualizar un contacto que no existe', function () {
    Sanctum::actingAs($this->user);

    $this->putJson('/api/v1/contacts/99999', ['first_name' => 'X'])
        ->assertNotFound();
});

// DELETE /api/v1/contacts/{id}

it('elimina un contacto propio con borrado lógico', function () {
    $contact = Contact::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);

    $this->deleteJson("/api/v1/contacts/{$contact->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Contacto eliminado correctamente.');

    $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
});

it('responde 404 al ver un contacto eliminado', function () {
    $contact = Contact::factory()->for($this->user)->create();
    $contact->delete();
    Sanctum::actingAs($this->user);

    $this->getJson("/api/v1/contacts/{$contact->id}")
        ->assertNotFound();
});

it('no deja eliminar el contacto de otro usuario', function () {
    $contact = Contact::factory()->for($this->otro)->create();
    Sanctum::actingAs($this->user);

    $this->deleteJson("/api/v1/contacts/{$contact->id}")
        ->assertForbidden();

    $this->assertNotSoftDeleted('contacts', ['id' => $contact->id]);
});

it('responde 404 al eliminar un contacto que no existe', function () {
    Sanctum::actingAs($this->user);

    $this->deleteJson('/api/v1/contacts/99999')
        ->assertNotFound();
});
