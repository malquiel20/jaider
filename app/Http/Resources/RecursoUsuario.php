<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Decide qué datos del usuario salen en el JSON (nunca la contraseña).
 */
class RecursoUsuario extends JsonResource
{
    public function toArray(Request $solicitud): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim($this->first_name.' '.$this->last_name),
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            // Solo aparece si el controlador contó los contactos con loadCount('contactos').
            'contacts_count' => $this->whenCounted('contactos'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
