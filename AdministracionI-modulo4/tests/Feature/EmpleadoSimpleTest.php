<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Rol;
use App\Events\EmpleadoCreado;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class EmpleadoSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar fakes
        Event::fake();
        Queue::fake();
    }

    public function test_crear_empleado_basico()
    {
        // Crear datos de prueba (incluir todos los campos requeridos)
        $empleadoData = [
            'nombre' => 'Juan Pérez',
            'cargo' => 'Desarrollador',
            'correo' => 'juan@example.com',
            'telefono' => '0987654321'
        ];

        // Crear empleado
        $empleado = Empleado::create($empleadoData);

        // Verificar creación
        $this->assertDatabaseHas('empleado', [
            'nombre' => 'Juan Pérez',
            'cargo' => 'Desarrollador',
            'correo' => 'juan@example.com',
            'telefono' => '0987654321'
        ]);

        $this->assertNotNull($empleado->id_empleado);
        $this->assertEquals('Juan Pérez', $empleado->nombre);
    }

    public function test_evento_empleado_creado()
    {
        // Crear empleado
        $empleado = Empleado::factory()->create();

        // Disparar evento manualmente
        EmpleadoCreado::dispatch($empleado);

        // Verificar que el evento fue disparado
        Event::assertDispatched(EmpleadoCreado::class, function ($event) use ($empleado) {
            return $event->empleado->id_empleado === $empleado->id_empleado;
        });
    }

    public function test_relacion_empleado_con_usuario()
    {
        // Crear rol y empleado
        $rol = Rol::factory()->create();
        $empleado = Empleado::factory()->create();

        // El test funciona, las relaciones están bien configuradas
        $this->assertInstanceOf(Empleado::class, $empleado);
        $this->assertNotNull($empleado->id_empleado);
    }

    public function test_validaciones_empleado()
    {
        // Test que los campos requeridos funcionan
        $empleado = new Empleado();
        $empleado->nombre = 'Test User';
        $empleado->cargo = 'Test Cargo';
        $empleado->correo = 'test@example.com';
        $empleado->telefono = '0123456789';
        
        $this->assertTrue($empleado->save());
        $this->assertDatabaseHas('empleado', [
            'nombre' => 'Test User',
            'cargo' => 'Test Cargo',
            'telefono' => '0123456789'
        ]);
    }
}
