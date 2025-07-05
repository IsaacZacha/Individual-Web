<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Empleado;
use App\Events\EmpleadoActualizado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class EmpleadoEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test directo del evento EmpleadoActualizado
     * @test
     */
    public function test_empleado_actualizado_evento_directo()
    {
        Event::fake();

        // Crear empleado
        $empleado = Empleado::factory()->create();

        // Disparar evento directamente
        event(new EmpleadoActualizado($empleado));

        // Verificar que el evento fue disparado
        Event::assertDispatched(EmpleadoActualizado::class);
    }

    /**
     * Test del resolver directamente
     * @test
     */
    public function test_resolver_actualizar_empleado_directo()
    {
        Event::fake();

        // Crear empleado
        $empleado = Empleado::factory()->create();

        // Usar el resolver directamente
        $resolver = new \App\GraphQL\Mutations\ActualizarEmpleado();
        $result = $resolver(null, [
            'id_empleado' => $empleado->id_empleado,
            'input' => [
                'nombre' => 'Nuevo Nombre',
                'cargo' => 'Nuevo Cargo'
            ]
        ]);

        // Verificar que el evento fue disparado
        Event::assertDispatched(EmpleadoActualizado::class);
        
        // Verificar que el empleado fue actualizado
        $this->assertEquals('Nuevo Nombre', $result->nombre);
        $this->assertEquals('Nuevo Cargo', $result->cargo);
    }
}
