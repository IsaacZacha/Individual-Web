<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_crear_rol()
    {
        $headers = $this->authenticate();
        $data = ['nombre' => 'Administrador'];

        $response = $this->withHeaders($headers)->postJson('/api/roles', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    public function test_listar_roles()
    {
        $headers = $this->authenticate();
        Rol::factory()->count(2)->create();

        $response = $this->withHeaders($headers)->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([['id_rol', 'nombre']]);
    }

    public function test_mostrar_rol()
    {
        $headers = $this->authenticate();
        $rol = Rol::factory()->create();

        $response = $this->withHeaders($headers)->getJson('/api/roles/' . $rol->id_rol);

        $response->assertStatus(200)
            ->assertJsonFragment(['id_rol' => $rol->id_rol, 'nombre' => $rol->nombre]);
    }

    public function test_actualizar_rol()
    {
        $headers = $this->authenticate();
        $rol = Rol::factory()->create();
        $data = ['nombre' => 'Rol Actualizado'];

        $response = $this->withHeaders($headers)->putJson('/api/roles/' . $rol->id_rol, $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);
    }

    public function test_no_autenticado_no_puede_acceder()
    {
        $response = $this->getJson('/api/roles');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}