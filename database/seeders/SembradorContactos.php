<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Llena la base con datos de prueba.
 */
class SembradorContactos extends Seeder
{
    /**
     * Dos usuarios de prueba (contraseña: password), cada uno con 5 contactos.
     */
    public function run(): void
    {
        $admin = Usuario::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Contactos',
            'email' => 'admin@contactos.test',
            'password' => 'password',
            'phone_number' => '3000000001',
        ]);

        $ana = Usuario::factory()->create([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'email' => 'ana@contactos.test',
            'password' => 'password',
            'phone_number' => '3000000002',
        ]);

        Contacto::factory(5)->for($admin)->create();
        Contacto::factory(5)->for($ana)->create();
    }
}
