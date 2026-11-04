<?php

namespace App\Http\Requests\Auth;

/**
 * Mensajes de validación en español compartidos por el registro y el perfil.
 */
class UserMessages
{
    public static function all(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre admite máximo 100 caracteres.',
            'last_name.max' => 'El apellido admite máximo 100 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Este correo ya está en uso por otro usuario.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'phone_number.regex' => 'El teléfono solo admite números (7 a 15 dígitos) y un + inicial opcional.',
            'phone_number.unique' => 'Este número de teléfono ya está en uso por otro usuario.',
            'device_name.required' => 'El nombre del dispositivo es obligatorio.',
        ];
    }
}
