<?php

namespace App\Models;

use Database\Factories\FabricaUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuario que inicia sesión en la API (tabla users).
 */
class Usuario extends Authenticatable
{
    /** @use HasFactory<FabricaUsuario> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * La tabla sigue llamándose users, como pide el documento del proyecto.
     */
    protected $table = 'users';

    /**
     * Campos que se pueden llenar con create() o update().
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
    ];

    /**
     * Campos que nunca salen en las respuestas JSON.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * La tabla users no tiene remember_token: la API usa tokens de Sanctum.
     */
    protected $rememberTokenName = '';

    protected function casts(): array
    {
        return [
            // Guarda siempre la contraseña encriptada (hash).
            'password' => 'hashed',
        ];
    }

    /**
     * Indica qué fábrica de datos de prueba usa este modelo.
     */
    protected static function newFactory(): FabricaUsuario
    {
        return FabricaUsuario::new();
    }

    /**
     * Un usuario tiene muchos contactos (contacts.user_id).
     */
    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class, 'user_id');
    }
}
