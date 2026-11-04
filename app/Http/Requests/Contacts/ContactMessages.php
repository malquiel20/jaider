<?php

namespace App\Http\Requests\Contacts;

/**
 * Mensajes de validación en español compartidos por crear y actualizar contactos.
 */
class ContactMessages
{
    public static function all(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre admite máximo 100 caracteres.',
            'last_name.max' => 'El apellido admite máximo 100 caracteres.',
            'phone_number.required' => 'El número de teléfono es obligatorio.',
            'phone_number.regex' => 'El teléfono solo admite números (7 a 15 dígitos) y un + inicial opcional.',
            'phone_number.unique' => 'Este número de teléfono ya está en uso por otro contacto.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Este correo ya está en uso por otro contacto.',
            'address.required' => 'La dirección de casa es obligatoria.',
            'address.max' => 'La dirección admite máximo 255 caracteres.',
            'avatar_url.url' => 'El avatar debe ser una URL válida.',
        ];
    }
}
