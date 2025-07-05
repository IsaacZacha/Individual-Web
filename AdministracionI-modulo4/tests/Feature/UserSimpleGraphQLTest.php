<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Rol;
use App\Models\Empleado;

class UserSimpleGraphQLTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_usuario_simple()
    {
        // Crear dependencias
        $empleado = Empleado::factory()->create();
        $rol = Rol::factory()->create();
        
        // Crear usuario
        $user = User::create([
            'empleado_id' => $empleado->id_empleado,
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'rol_id' => $rol->id_rol
        ]);
        
        $this->assertNotNull($user->id_usuario);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals($empleado->id_empleado, $user->empleado_id);
        $this->assertEquals($rol->id_rol, $user->rol_id);
        
        // Verificar relaciones
        $this->assertNotNull($user->empleado);
        $this->assertNotNull($user->rol);
        $this->assertEquals($empleado->nombre, $user->empleado->nombre);
        $this->assertEquals($rol->nombre, $user->rol->nombre);
    }
}
