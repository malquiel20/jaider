<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\StoreContactRequest;
use App\Http\Requests\Contacts\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ContactController extends Controller
{
    /**
     * Lista los contactos del usuario autenticado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $contacts = $request->user()
            ->contacts()
            ->orderBy('first_name')
            ->paginate(15);

        return ContactResource::collection($contacts);
    }

    /**
     * Crea un contacto para el usuario autenticado.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $request->user()->contacts()->create($request->validated());

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Perfil de un contacto (toda su información).
     */
    public function show(Contact $contact): ContactResource
    {
        Gate::authorize('view', $contact);

        return new ContactResource($contact);
    }

    /**
     * Actualiza un contacto propio.
     */
    public function update(UpdateContactRequest $request, Contact $contact): ContactResource
    {
        // La propiedad del contacto ya se revisó en UpdateContactRequest::authorize().
        $contact->update($request->validated());

        return new ContactResource($contact);
    }

    /**
     * Elimina un contacto propio (borrado lógico: llena deleted_at).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        Gate::authorize('delete', $contact);

        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente.',
        ]);
    }
}
