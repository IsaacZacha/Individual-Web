<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadoTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_crear_empleado()
    {
        $headers = $this->authenticate();
        $data = [
            'nombre' => 'Juan Pérez',
            'cargo' => 'Gerente',
            'correo' => 'juan@empresa.com',
            'telefono' => '0999999999'
        ];

        $response = $this->withHeaders($headers)->postJson('/api/empleados', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    public function test_listar_empleados()
    {
        $headers = $this->authenticate();
        Empleado::factory()->count(2)->create();

        $response = $this->withHeaders($headers)->getJson('/api/empleados');

        $response->assertStatus(200)
            ->assertJsonStructure([['id_empleado', 'nombre', 'cargo', 'correo', 'telefono']]);
    }

    public function test_mostrar_empleado()
    {
        $headers = $this->authenticate();
        $empleado = Empleado::factory()->create();

        $response = $this->withHeaders($headers)->getJson('/api/empleados/' . $empleado->id_empleado);

        $response->assertStatus(200)
            ->assertJsonFragment(['id_empleado' => $empleado->id_empleado]);
    }

    public function test_actualizar_empleado()
    {
        $headers = $this->authenticate();
        $empleado = Empleado::factory()->create();
        $data = [
            'nombre' => 'Juan Actualizado',
            'cargo' => 'Subgerente',
            'correo' => 'juan.actualizado@empresa.com',
            'telefono' => '0987654321'
        ];

        $response = $this->withHeaders($headers)->putJson('/api/empleados/' . $empleado->id_empleado, $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);
    }

    public function test_eliminar_empleado()
    {
        $headers = $this->authenticate();
        $empleado = Empleado::factory()->create();

        $response = $this->withHeaders($headers)->deleteJson('/api/empleados/' . $empleado->id_empleado);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Empleado eliminado']);
        $this->assertDatabaseMissing('empleado', ['id_empleado' => $empleado->id_empleado]);
    }

    public function test_no_autenticado_no_puede_acceder()
    {
        $response = $this->getJson('/api/empleados');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}