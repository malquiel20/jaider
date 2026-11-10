<?php

namespace App\Http\Requests\Autenticacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida los datos para actualizar el perfil del usuario (PUT /auth/me).
 */
class SolicitudActualizarPerfil extends FormRequest
{
    /**
     * Cada usuario solo actualiza su propio perfil, así que siempre se permite.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación. "sometimes" = solo se valida si el campo viene en la petición.
     * ignore() deja que el usuario conserve su propio correo y teléfono.
     */
    public function rules(): array
    {
        $idUsuario = $this->user()->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($idUsuario)],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9]{7,15}$/', Rule::unique('users', 'phone_number')->ignore($idUsuario)],
        ];
    }

    /**
     * Mensajes de error en español.
     */
    public function messages(): array
    {
        return MensajesUsuario::todos();
    }
}
