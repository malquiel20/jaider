<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Dos usuarios de prueba (contraseña: password), cada uno con 5 contactos.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Contactos',
            'email' => 'admin@contactos.test',
            'password' => 'password',
            'phone_number' => '3000000001',
        ]);

        $ana = User::factory()->create([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'email' => 'ana@contactos.test',
            'password' => 'password',
            'phone_number' => '3000000002',
        ]);

        Contact::factory(5)->for($admin)->create();
        Contact::factory(5)->for($ana)->create();
    }
}
