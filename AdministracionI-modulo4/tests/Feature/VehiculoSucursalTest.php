<?php

namespace Tests\Feature;

use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\VehiculoSucursal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoSucursalTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_crear_vehiculo_sucursal()
    {
        $headers = $this->authenticate();
        $vehiculo = Vehiculo::factory()->create();
        $sucursal = Sucursal::factory()->create();

        $data = [
            'vehiculo_id' => $vehiculo->id_vehiculo,
            'sucursal_id' => $sucursal->id_sucursal,
            'fecha_asignacion' => '2024-07-01'
        ];

        $response = $this->withHeaders($headers)->postJson('/api/vehiculo-sucursal', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'id_vehiculo' => $data['vehiculo_id'],
                'id_sucursal' => $data['sucursal_id']
            ]);
    }

    public function test_mostrar_vehiculo_sucursal()
    {
        $headers = $this->authenticate();
        $vehiculoSucursal = VehiculoSucursal::factory()->create();

        $response = $this->withHeaders($headers)->getJson('/api/vehiculo-sucursal/' . $vehiculoSucursal->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id_vehiculo' => $vehiculoSucursal->id_vehiculo,
                'id_sucursal' => $vehiculoSucursal->id_sucursal
            ]);
    }

    public function test_actualizar_vehiculo_sucursal()
    {
        $headers = $this->authenticate();
        $vehiculoSucursal = VehiculoSucursal::factory()->create();
        $data = [
            'vehiculo_id' => $vehiculoSucursal->id_vehiculo,
            'sucursal_id' => $vehiculoSucursal->id_sucursal,
            'fecha_asignacion' => '2024-08-01'
        ];

        $response = $this->withHeaders($headers)->putJson('/api/vehiculo-sucursal/' . $vehiculoSucursal->id, $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id_vehiculo' => $data['vehiculo_id'],
                'id_sucursal' => $data['sucursal_id']
            ]);
    }

    public function test_no_autenticado_no_puede_acceder()
    {
        $response = $this->getJson('/api/vehiculo-sucursal');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}