<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Un usuario solo puede ver, editar o eliminar sus propios contactos.
 */
class ContactPolicy
{
    public function view(User $user, Contact $contact): Response
    {
        return $this->owns($user, $contact);
    }

    public function update(User $user, Contact $contact): Response
    {
        return $this->owns($user, $contact);
    }

    public function delete(User $user, Contact $contact): Response
    {
        return $this->owns($user, $contact);
    }

    private function owns(User $user, Contact $contact): Response
    {
        return (int) $user->id === (int) $contact->user_id
            ? Response::allow()
            : Response::deny('No tienes permiso para acceder a este contacto.');
    }
}
