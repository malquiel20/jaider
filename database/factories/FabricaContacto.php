<?php

namespace Database\Factories;

use App\Models\Contacto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Crea contactos falsos para las pruebas y el sembrador.
 *
 * @extends Factory<Contacto>
 */
class FabricaContacto extends Factory
{
    /**
     * Modelo que crea esta fábrica.
     */
    protected $model = Contacto::class;

    /**
     * Datos por defecto de cada contacto falso.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => Usuario::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->unique()->numerify('3#########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->streetAddress(),
            'avatar_url' => null,
        ];
    }
}
