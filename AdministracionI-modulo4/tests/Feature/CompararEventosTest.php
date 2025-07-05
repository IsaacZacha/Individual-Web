<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Events\EmpleadoCreado;
use App\Events\SucursalCreada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;

class CompararEventosTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     * Comparar eventos Empleado vs Sucursal
     */
    public function test_comparar_eventos_empleado_vs_sucursal()
    {
        Event::fake();
        
        // Test Empleado
        $empleado = Empleado::create([
            'nombre' => 'Test Empleado',
            'cargo' => 'Test Cargo',
            'correo' => 'test@test.com',
            'telefono' => '123456789'
        ]);
        
        event(new EmpleadoCreado($empleado));
        
        // Test Sucursal
        $sucursal = Sucursal::create([
            'nombre' => 'Test Sucursal',
            'direccion' => 'Test Direccion',
            'ciudad' => 'Test Ciudad',
            'telefono' => '123456789'
        ]);
        
        event(new SucursalCreada($sucursal));
        
        // Verificar ambos eventos
        Event::assertDispatched(EmpleadoCreado::class);
        Event::assertDispatched(SucursalCreada::class);
        
        $this->assertTrue(true);
    }
}
