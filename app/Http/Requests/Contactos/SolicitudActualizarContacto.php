<?php

namespace App\Http\Requests\Contactos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Valida los datos para actualizar un contacto (PUT /contacts/{contacto}).
 */
class SolicitudActualizarContacto extends FormRequest
{
    /**
     * Antes de validar, revisa que el contacto sea del usuario (PoliticaContacto).
     * Si no es suyo, responde 403 sin mirar los datos.
     */
    public function authorize(): bool
    {
        Gate::authorize('actualizar', $this->route('contacto'));

        return true;
    }

    /**
     * Reglas de validación. "sometimes" = solo se valida si el campo viene en la petición.
     * ignore() deja que el contacto conserve su propio correo y teléfono.
     */
    public function rules(): array
    {
        $idContacto = $this->route('contacto')->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'phone_number' => ['sometimes', 'required', 'string', 'regex:/^\+?[0-9]{7,15}$/', Rule::unique('contacts', 'phone_number')->ignore($idContacto)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($idContacto)],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Mensajes de error en español.
     */
    public function messages(): array
    {
        return MensajesContacto::todos();
    }
}
