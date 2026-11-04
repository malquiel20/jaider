<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
    ];

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
            'password' => 'hashed',
        ];
    }

    /**
     * Un usuario tiene muchos contactos.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
