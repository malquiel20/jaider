<?php

namespace App\Http\Requests\Autenticacion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida los datos del registro de un usuario nuevo (POST /auth/register).
 */
class SolicitudRegistro extends FormRequest
{
    /**
     * Cualquiera puede registrarse.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación. El correo y el teléfono no se pueden repetir entre usuarios.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['nullable', 'string', 'regex:/^\+?[0-9]{7,15}$/', 'unique:users,phone_number'],
            'device_name' => ['required', 'string', 'max:100'],
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
