<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Events\UserCreado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_usuario_basico()
    {
        // Crear rol y empleado primero
        $rol = Rol::factory()->create([
            'nombre' => 'Admin'
        ]);

        $empleado = \App\Models\Empleado::factory()->create();

        // Datos del usuario
        $userData = [
            'empleado_id' => $empleado->id_empleado,
            'username' => 'test_user',
            'password' => 'password123',
            'rol_id' => $rol->id_rol
        ];

        // Crear usuario
        $user = User::create($userData);

        // Verificar creación
        $this->assertDatabaseHas('users', [
            'empleado_id' => $empleado->id_empleado,
            'username' => 'test_user',
            'rol_id' => $rol->id_rol
        ]);

        $this->assertNotNull($user->id_usuario);
        $this->assertEquals('test_user', $user->username);
    }

    public function test_relacion_usuario_rol()
    {
        // Crear rol y usuario
        $rol = Rol::factory()->create(['nombre' => 'Manager']);
        $user = User::factory()->create(['rol_id' => $rol->id_rol]);

        // Verificar relación
        $this->assertInstanceOf(Rol::class, $user->rol);
        $this->assertEquals('Manager', $user->rol->nombre);
    }
}
