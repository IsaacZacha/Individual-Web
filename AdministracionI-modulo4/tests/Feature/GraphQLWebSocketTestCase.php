<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Broadcast;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

/**
 * Base class para tests de GraphQL y WebSocket
 * Proporciona métodos comunes para testing
 */

abstract class GraphQLWebSocketTestCase extends TestCase
{
    use RefreshDatabase, WithFaker, MakesGraphQLRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar eventos y colas para testing
        Event::fake();
        Queue::fake();
        
        // RefreshDatabase se encarga de la migración automáticamente
    }

    /**
     * Helper para ejecutar mutation GraphQL
     */
    
    protected function executeMutation(string $mutation, array $variables = [])
    {
        return $this->graphQL($mutation, $variables);
    }

    /**
     * Helper para ejecutar query GraphQL
     */
    protected function executeQuery(string $query, array $variables = [])
    {
        return $this->graphQL($query, $variables);
    }

    /**
     * Verificar que un evento fue disparado
     */
    protected function assertEventDispatched(string $eventClass, $callback = null)
    {
        Event::assertDispatched($eventClass, $callback);
    }

    /**
     * Verificar que un evento NO fue disparado
     */
    protected function assertEventNotDispatched(string $eventClass)
    {
        Event::assertNotDispatched($eventClass);
    }

    /**
     * Verificar estructura de respuesta GraphQL exitosa
     */
    protected function assertGraphQLSuccess($response, string $operation)
    {
        $response->assertOk();
        
        // Debug: Mostrar el contenido de la respuesta si falla
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        
        if (!isset($decoded['data'])) {
            echo "Response content: " . substr($content, 0, 1000) . "\n";
        }
        
        $response->assertJsonStructure([
            'data' => [
                $operation => []
            ]
        ]);
        $response->assertJsonMissing(['errors']);
    }

    /**
     * Verificar error en respuesta GraphQL
     */
    protected function assertGraphQLError($response, string $expectedMessage = null)
    {
        $response->assertJsonStructure(['errors']);
        
        if ($expectedMessage) {
            $response->assertJsonFragment(['message' => $expectedMessage]);
        }
    }

    /**
     * Verificar que un evento de broadcasting contiene datos específicos
     */
    protected function assertBroadcastEventData(string $eventClass, array $expectedData)
    {
        Event::assertDispatched($eventClass, function ($event) use ($expectedData) {
            $broadcastData = $event->broadcastWith();
            
            foreach ($expectedData as $key => $value) {
                if (!isset($broadcastData[$key]) || $broadcastData[$key] !== $value) {
                    return false;
                }
            }
            
            return true;
        });
    }

    /**
     * Verificar canales de broadcasting
     */
    protected function assertBroadcastChannels(string $eventClass, array $expectedChannels)
    {
        Event::assertDispatched($eventClass, function ($event) use ($expectedChannels) {
            $channels = collect($event->broadcastOn())->map(function ($channel) {
                return $channel->name;
            })->toArray();
            
            return empty(array_diff($expectedChannels, $channels));
        });
    }

    /**
     * Crear datos de prueba básicos
     */
    protected function createTestData()
    {
        // Crear dependencias primero
        $empleado = \App\Models\Empleado::factory()->create();
        $rol = \App\Models\Rol::factory()->create();
        
        return [
            'empleado' => [
                'nombre' => $this->faker->name,
                'cargo' => $this->faker->jobTitle,
                'correo' => $this->faker->unique()->safeEmail,
                'telefono' => $this->faker->phoneNumber
            ],
            'vehiculo' => [
                'placa' => strtoupper($this->faker->bothify('???###')),
                'marca' => $this->faker->randomElement(['Toyota', 'Honda', 'Nissan', 'Ford']),
                'modelo' => $this->faker->word,
                'anio' => $this->faker->numberBetween(2010, 2024),
                'tipo_id' => $this->faker->randomElement(['sedan', 'suv', 'pickup']),
                'estado' => $this->faker->randomElement(['disponible', 'ocupado', 'mantenimiento'])
            ],
            'sucursal' => [
                'nombre' => $this->faker->company,
                'direccion' => $this->faker->address,
                'ciudad' => $this->faker->city,
                'telefono' => $this->faker->phoneNumber
            ],
            'rol' => [
                'nombre' => $this->faker->jobTitle,
                'descripcion' => $this->faker->sentence
            ],
            'user' => [
                'empleado_id' => $empleado->id_empleado,
                'username' => $this->faker->unique()->userName,
                'password' => 'password123',
                'rol_id' => $rol->id_rol
            ]
        ];
    }
}
