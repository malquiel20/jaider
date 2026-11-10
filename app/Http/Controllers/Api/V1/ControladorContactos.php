<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contactos\SolicitudActualizarContacto;
use App\Http\Requests\Contactos\SolicitudCrearContacto;
use App\Http\Resources\RecursoContacto;
use App\Models\Contacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

/**
 * CRUD de los contactos del usuario que inició sesión.
 */
class ControladorContactos extends Controller
{
    /**
     * GET /contacts: lista los contactos del usuario (15 por página).
     * Los contactos eliminados (soft delete) no aparecen.
     */
    public function listar(Request $solicitud): AnonymousResourceCollection
    {
        $contactos = $solicitud->user()
            ->contactos()
            ->orderBy('first_name')
            ->paginate(15);

        return RecursoContacto::collection($contactos);
    }

    /**
     * POST /contacts: crea un contacto para el usuario.
     */
    public function crear(SolicitudCrearContacto $solicitud): JsonResponse
    {
        $contacto = $solicitud->user()->contactos()->create($solicitud->validated());

        return (new RecursoContacto($contacto))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /contacts/{contacto}: perfil de un contacto (toda su información).
     * Laravel busca el contacto por el id de la URL; si no existe, responde 404.
     */
    public function ver(Contacto $contacto): RecursoContacto
    {
        // Si el contacto es de otro usuario, responde 403.
        Gate::authorize('ver', $contacto);

        return new RecursoContacto($contacto);
    }

    /**
     * PUT /contacts/{contacto}: actualiza un contacto propio.
     */
    public function actualizar(SolicitudActualizarContacto $solicitud, Contacto $contacto): RecursoContacto
    {
        // La propiedad del contacto ya se revisó en SolicitudActualizarContacto::authorize().
        $contacto->update($solicitud->validated());

        return new RecursoContacto($contacto);
    }

    /**
     * DELETE /contacts/{contacto}: elimina un contacto propio.
     * Es borrado lógico: solo llena deleted_at, el registro sigue en la base.
     */
    public function eliminar(Contacto $contacto): JsonResponse
    {
        Gate::authorize('eliminar', $contacto);

        $contacto->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente.',
        ]);
    }
}
