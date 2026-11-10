<?php

namespace App\Http\Requests\Contactos;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida los datos para crear un contacto (POST /contacts).
 */
class SolicitudCrearContacto extends FormRequest
{
    /**
     * Cualquier usuario con sesión iniciada puede crear contactos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación. El correo y el teléfono no se pueden repetir entre contactos.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/', 'unique:contacts,phone_number'],
            'email' => ['required', 'email', 'max:255', 'unique:contacts,email'],
            'address' => ['required', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:255'],
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
