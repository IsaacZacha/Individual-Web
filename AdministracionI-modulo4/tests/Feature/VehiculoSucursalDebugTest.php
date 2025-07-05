<?php

namespace Tests\Feature;

use App\Models\VehiculoSucursal;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoSucursalDebugTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_debug_vehiculo_sucursal_delete()
    {
        $headers = $this->authenticate();
        
        // Crear vehículo y sucursal primero
        $vehiculo = Vehiculo::factory()->create();
        $sucursal = Sucursal::factory()->create();
        
        // Crear la relación
        $vehiculoSucursal = VehiculoSucursal::factory()->create([
            'id_vehiculo' => $vehiculo->id_vehiculo,
            'id_sucursal' => $sucursal->id_sucursal
        ]);

        echo "\n=== DEBUG VEHICULO SUCURSAL DELETE ===\n";
        echo "VehiculoSucursal ID: " . $vehiculoSucursal->id . "\n";
        echo "Vehiculo ID: " . $vehiculo->id_vehiculo . "\n";
        echo "Sucursal ID: " . $sucursal->id_sucursal . "\n";
        
        // Verificar que existe antes de eliminar
        $existsBefore = VehiculoSucursal::where('id', $vehiculoSucursal->id)->exists();
        echo "Existe antes de DELETE: " . ($existsBefore ? 'SÍ' : 'NO') . "\n";
        
        // Probar GET primero
        $getResponse = $this->withHeaders($headers)->getJson('/api/vehiculo-sucursal/' . $vehiculoSucursal->id);
        echo "GET Status: " . $getResponse->getStatusCode() . "\n";
        
        // Intentar eliminar
        $deleteResponse = $this->withHeaders($headers)->deleteJson('/api/vehiculo-sucursal/' . $vehiculoSucursal->id);
        echo "DELETE Status: " . $deleteResponse->getStatusCode() . "\n";
        echo "DELETE Response: " . $deleteResponse->getContent() . "\n";
        
        // Verificar qué pasó en la base de datos
        $existsAfter = VehiculoSucursal::where('id', $vehiculoSucursal->id)->exists();
        echo "Existe después de DELETE: " . ($existsAfter ? 'SÍ' : 'NO') . "\n";
        
        $this->assertTrue(true);
    }
}
