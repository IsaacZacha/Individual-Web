<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SucursalTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_crear_sucursal()
    {
        $headers = $this->authenticate();
        $data = ['nombre' => 'Sucursal Uno', 'direccion' => 'Calle 123', 'ciudad' => 'Quito', 'telefono' => '0999999999'];

        $response = $this->withHeaders($headers)->postJson('/api/sucursales', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    public function test_listar_sucursales()
    {
        $headers = $this->authenticate();
        Sucursal::factory()->count(2)->create();

        $response = $this->withHeaders($headers)->getJson('/api/sucursales');

        $response->assertStatus(200)
            ->assertJsonStructure([['id_sucursal', 'nombre', 'direccion', 'ciudad', 'telefono']]);
    }

    public function test_mostrar_sucursal()
    {
        $headers = $this->authenticate();
        $sucursal = Sucursal::factory()->create();

        $response = $this->withHeaders($headers)->getJson('/api/sucursales/' . $sucursal->id_sucursal);

        $response->assertStatus(200)
            ->assertExactJson($sucursal->fresh()->toArray());
    }

    public function test_actualizar_sucursal()
    {
        $headers = $this->authenticate();
        $sucursal = Sucursal::factory()->create();
        $data = ['nombre' => 'Sucursal Actualizada', 'direccion' => 'Nueva Dirección', 'ciudad' => 'Nueva Ciudad', 'telefono' => '0987654321'];

        $response = $this->withHeaders($headers)->putJson('/api/sucursales/' . $sucursal->id_sucursal, $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);
    }

    public function test_eliminar_sucursal()
    {
        $headers = $this->authenticate();
        $sucursal = Sucursal::factory()->create();

        $response = $this->withHeaders($headers)->deleteJson('/api/sucursales/' . $sucursal->id_sucursal);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Sucursal eliminada']);
        $this->assertDatabaseMissing('sucursal', ['id_sucursal' => $sucursal->id_sucursal]);
    }

    public function test_no_autenticado_no_puede_acceder()
    {
        $response = $this->getJson('/api/sucursales');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}