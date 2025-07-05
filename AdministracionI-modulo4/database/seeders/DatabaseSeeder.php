<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use App\Models\Empleado;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear primero los datos base
        $rol = Rol::factory()->create(['nombre' => 'Admin']);
        $empleado = Empleado::factory()->create(['nombre' => 'Test User']);

        // Crear usuario de prueba
        User::factory()->create([
            'empleado_id' => $empleado->id_empleado,
            'username' => 'testuser',
            'rol_id' => $rol->id_rol,
        ]);
    }
}
