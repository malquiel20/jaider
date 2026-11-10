<?php

namespace App\Models;

use Database\Factories\FabricaContacto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Contacto de un usuario (tabla contacts).
 */
class Contacto extends Model
{
    /** @use HasFactory<FabricaContacto> */
    use HasFactory, SoftDeletes;

    /**
     * La tabla sigue llamándose contacts, como pide el documento del proyecto.
     */
    protected $table = 'contacts';

    /**
     * Campos que se pueden llenar con create() o update().
     * user_id no está aquí: se asigna desde la relación del usuario.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'address',
        'avatar_url',
    ];

    /**
     * Indica qué fábrica de datos de prueba usa este modelo.
     */
    protected static function newFactory(): FabricaContacto
    {
        return FabricaContacto::new();
    }

    /**
     * Un contacto pertenece a un usuario (contacts.user_id).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
