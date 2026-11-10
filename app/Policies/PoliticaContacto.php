<?php

namespace App\Policies;

use App\Models\Contacto;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Permisos sobre los contactos: un usuario solo puede ver, editar o
 * eliminar sus propios contactos. Se registra en AppServiceProvider.
 */
class PoliticaContacto
{
    /**
     * Gate::authorize('ver', $contacto)
     */
    public function ver(Usuario $usuario, Contacto $contacto): Response
    {
        return $this->esDueno($usuario, $contacto);
    }

    /**
     * Gate::authorize('actualizar', $contacto)
     */
    public function actualizar(Usuario $usuario, Contacto $contacto): Response
    {
        return $this->esDueno($usuario, $contacto);
    }

    /**
     * Gate::authorize('eliminar', $contacto)
     */
    public function eliminar(Usuario $usuario, Contacto $contacto): Response
    {
        return $this->esDueno($usuario, $contacto);
    }

    /**
     * Permite si el contacto es del usuario; si no, responde 403 con el mensaje.
     */
    private function esDueno(Usuario $usuario, Contacto $contacto): Response
    {
        return (int) $usuario->id === (int) $contacto->user_id
            ? Response::allow()
            : Response::deny('No tienes permiso para acceder a este contacto.');
    }
}
