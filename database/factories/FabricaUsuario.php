<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Crea usuarios falsos para las pruebas y el sembrador.
 *
 * @extends Factory<Usuario>
 */
class FabricaUsuario extends Factory
{
    /**
     * Modelo que crea esta fábrica.
     */
    protected $model = Usuario::class;

    /**
     * Contraseña encriptada que comparten todos los usuarios falsos ("password").
     */
    protected static ?string $contrasena;

    /**
     * Datos por defecto de cada usuario falso.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$contrasena ??= Hash::make('password'),
            'phone_number' => fake()->unique()->numerify('3#########'),
        ];
    }
}
