<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

/**
 * Controlador para pruebas completas del sistema desde Postman
 */
class SystemTestController extends Controller
{
    /**
     * Crear empleado con evento de RabbitMQ
     */
    public function createEmpleadoWithEvent(Request $request): JsonResponse
    {
        try {
            $empleadoData = $request->input('empleado');
            $publishEvent = $request->input('publish_event', false);

            // Simular creación de empleado (aquí llamarías a GraphQL)
            $empleado = [
                'id_empleado' => 'EMP-' . rand(1000, 9999),
                'nombre' => $empleadoData['nombre'],
                'apellido' => $empleadoData['apellido'],
                'dni' => $empleadoData['dni'],
                'telefono' => $empleadoData['telefono'],
                'id_rol' => $empleadoData['id_rol'],
                'created_at' => now()->toISOString()
            ];

            $response = [
                'status' => 'success',
                'message' => '✅ Empleado creado exitosamente',
                'data' => [
                    'empleado' => $empleado,
                    'event_published' => false
                ]
            ];

            // Publicar evento si se solicita
            if ($publishEvent) {
                $rabbitmq = app(\App\Services\RabbitMQService::class);
                $rabbitmq->publishEmpleadoEvent($empleado, 'created');
                
                $response['data']['event_published'] = true;
                $response['data']['rabbitmq_queue'] = 'queue.empleados';
                $response['message'] = '✅ Empleado creado y evento publicado en RabbitMQ';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error creando empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear vehículo, asignar a sucursal y publicar eventos
     */
    public function createVehiculoAssignWithEvents(Request $request): JsonResponse
    {
        try {
            $vehiculoData = $request->input('vehiculo');
            $sucursalId = $request->input('sucursal_id');
            $publishEvents = $request->input('publish_events', false);

            // Simular creación de vehículo
            $vehiculo = [
                'id_vehiculo' => 'VEH-' . rand(1000, 9999),
                'placa' => $vehiculoData['placa'],
                'marca' => $vehiculoData['marca'],
                'modelo' => $vehiculoData['modelo'],
                'anio' => $vehiculoData['anio'],
                'tipo_id' => $vehiculoData['tipo_id'],
                'estado' => $vehiculoData['estado'],
                'created_at' => now()->toISOString()
            ];

            // Simular asignación
            $asignacion = [
                'id' => 'ASG-' . rand(1000, 9999),
                'id_vehiculo' => $vehiculo['id_vehiculo'],
                'id_sucursal' => $sucursalId,
                'fecha_asignacion' => now()->toISOString(),
                'created_at' => now()->toISOString()
            ];

            $response = [
                'status' => 'success',
                'message' => '✅ Vehículo creado y asignado exitosamente',
                'data' => [
                    'vehiculo' => $vehiculo,
                    'asignacion' => $asignacion,
                    'events_published' => []
                ]
            ];

            // Publicar eventos si se solicita
            if ($publishEvents) {
                $rabbitmq = app(\App\Services\RabbitMQService::class);
                
                // Evento de creación de vehículo
                $rabbitmq->publishVehiculoEvent($vehiculo, 'created');
                $response['data']['events_published'][] = 'vehiculo_created';
                
                // Evento de asignación
                $rabbitmq->publishAsignacionEvent($asignacion, 'assigned');
                $response['data']['events_published'][] = 'vehiculo_assigned';
                
                $response['message'] = '✅ Vehículo creado, asignado y eventos publicados en RabbitMQ';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error en flujo completo de vehículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los datos del dashboard
     */
    public function getDashboardData(): JsonResponse
    {
        try {
            // Simular consulta GraphQL
            $query = '
                query {
                    estadisticas {
                        totalEmpleados
                        totalVehiculos
                        totalSucursales
                        totalUsuarios
                        vehiculosPorEstado {
                            estado
                            count
                        }
                    }
                    empleados {
                        id_empleado
                        nombre
                        cargo
                        correo
                    }
                    vehiculos {
                        id_vehiculo
                        placa
                        marca
                        modelo
                        estado
                    }
                    sucursales {
                        id_sucursal
                        nombre
                        direccion
                        ciudad
                    }
                    users {
                        id
                        name
                        email
                    }
                    vehiculoSucursales {
                        id
                        id_vehiculo
                        id_sucursal
                        fecha_asignacion
                    }
                }
            ';

            // Aquí harías la consulta real a GraphQL
            // Por ahora devolvemos datos de ejemplo
            $data = [
                'estadisticas' => [
                    'totalEmpleados' => rand(5, 25),
                    'totalVehiculos' => rand(10, 50),
                    'totalSucursales' => rand(3, 10),
                    'totalUsuarios' => rand(5, 15),
                    'vehiculosPorEstado' => [
                        ['estado' => 'disponible', 'count' => rand(5, 15)],
                        ['estado' => 'ocupado', 'count' => rand(3, 10)],
                        ['estado' => 'mantenimiento', 'count' => rand(1, 5)]
                    ]
                ],
                'empleados' => [
                    ['id_empleado' => 'EMP-001', 'nombre' => 'Juan Pérez', 'cargo' => 'Conductor', 'correo' => 'juan@empresa.com'],
                    ['id_empleado' => 'EMP-002', 'nombre' => 'María López', 'cargo' => 'Supervisora', 'correo' => 'maria@empresa.com']
                ],
                'vehiculos' => [
                    ['id_vehiculo' => 'VEH-001', 'placa' => 'ABC-123', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'estado' => 'disponible'],
                    ['id_vehiculo' => 'VEH-002', 'placa' => 'DEF-456', 'marca' => 'Honda', 'modelo' => 'Civic', 'estado' => 'ocupado']
                ],
                'sucursales' => [
                    ['id_sucursal' => 'SUC-001', 'nombre' => 'Sucursal Central', 'direccion' => 'Av. Principal 123', 'ciudad' => 'Lima'],
                    ['id_sucursal' => 'SUC-002', 'nombre' => 'Sucursal Norte', 'direccion' => 'Av. Norte 456', 'ciudad' => 'Lima']
                ],
                'users' => [
                    ['id' => '1', 'name' => 'Admin User', 'email' => 'admin@sistema.com'],
                    ['id' => '2', 'name' => 'Regular User', 'email' => 'user@sistema.com']
                ],
                'vehiculoSucursales' => [
                    ['id' => 'ASG-001', 'id_vehiculo' => 'VEH-001', 'id_sucursal' => 'SUC-001', 'fecha_asignacion' => '2025-07-26T10:00:00Z'],
                    ['id' => 'ASG-002', 'id_vehiculo' => 'VEH-002', 'id_sucursal' => 'SUC-002', 'fecha_asignacion' => '2025-07-26T11:00:00Z']
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => '📊 Datos del dashboard obtenidos exitosamente',
                'data' => $data,
                'metadata' => [
                    'query_executed' => 'dashboard_complete_data',
                    'timestamp' => now()->toISOString(),
                    'total_entities' => [
                        'empleados' => count($data['empleados']),
                        'vehiculos' => count($data['vehiculos']),
                        'sucursales' => count($data['sucursales']),
                        'usuarios' => count($data['users']),
                        'asignaciones' => count($data['vehiculoSucursales'])
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error obteniendo datos del dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ejecutar comandos artisan desde Postman
     */
    public function runArtisanCommand(Request $request): JsonResponse
    {
        $request->validate([
            'command' => 'required|string|in:rabbitmq:test,microservice:work,queue:work'
        ]);

        try {
            $command = $request->input('command');
            $arguments = $request->input('arguments', []);

            $output = '';
            $exitCode = Artisan::call($command, $arguments);
            $output = Artisan::output();

            return response()->json([
                'status' => $exitCode === 0 ? 'success' : 'error',
                'message' => $exitCode === 0 ? "✅ Comando '{$command}' ejecutado exitosamente" : "❌ Error ejecutando comando '{$command}'",
                'data' => [
                    'command' => $command,
                    'arguments' => $arguments,
                    'exit_code' => $exitCode,
                    'output' => $output,
                    'timestamp' => now()->toISOString()
                ]
            ], $exitCode === 0 ? 200 : 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error ejecutando comando artisan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Información general del sistema
     */
    public function getSystemInfo(): JsonResponse
    {
        try {
            $rabbitmq = app(\App\Services\RabbitMQService::class);
            $rabbitmqHealth = $rabbitmq->healthCheck();

            return response()->json([
                'status' => 'success',
                'message' => '🚀 Información del sistema obtenida',
                'data' => [
                    'system' => [
                        'name' => 'Sistema de Administración de Vehículos',
                        'version' => '1.0.0',
                        'environment' => app()->environment(),
                        'php_version' => PHP_VERSION,
                        'laravel_version' => app()->version()
                    ],
                    'services' => [
                        'graphql' => [
                            'status' => 'active',
                            'endpoint' => '/graphql',
                            'playground' => '/graphiql'
                        ],
                        'rabbitmq' => [
                            'status' => $rabbitmqHealth['status'],
                            'host' => 'shark.rmq.cloudamqp.com',
                            'ssl' => true,
                            'provider' => 'CloudAMQP'
                        ],
                        'websocket' => [
                            'status' => 'configured',
                            'host' => '127.0.0.1:8080',
                            'channels' => ['empleados', 'vehiculos', 'sucursales', 'usuarios', 'asignaciones']
                        ]
                    ],
                    'api_endpoints' => [
                        'graphql' => '/graphql',
                        'dashboard' => '/dashboard',
                        'rabbitmq_health' => '/api/rabbitmq/health',
                        'rabbitmq_publish' => '/api/rabbitmq/publish',
                        'system_test' => '/api/test/system'
                    ],
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error obteniendo información del sistema',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
