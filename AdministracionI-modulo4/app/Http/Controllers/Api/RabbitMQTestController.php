<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RabbitMQService;
use App\Services\MicroserviceEventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para probar RabbitMQ desde Postman
 */
class RabbitMQTestController extends Controller
{
    protected $rabbitmqService;

    public function __construct(RabbitMQService $rabbitmqService)
    {
        $this->rabbitmqService = $rabbitmqService;
    }

    /**
     * Health check de RabbitMQ
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $health = $this->rabbitmqService->healthCheck();
            $stats = $this->rabbitmqService->getQueueStats();

            return response()->json([
                'status' => 'success',
                'message' => '🐰 RabbitMQ CloudAMQP funcionando correctamente',
                'data' => [
                    'health' => $health,
                    'queue_stats' => $stats,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error conectando con RabbitMQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publicar evento en RabbitMQ
     */
    public function publishEvent(Request $request): JsonResponse
    {
        $request->validate([
            'entity_type' => 'required|string|in:empleado,vehiculo,sucursal,usuario,vehiculo_sucursal',
            'action' => 'required|string|in:created,updated,deleted,assigned,unassigned,status_changed',
            'data' => 'required|array'
        ]);

        try {
            $entityType = $request->input('entity_type');
            $action = $request->input('action');
            $data = $request->input('data');

            // Publicar usando RabbitMQ directo
            switch ($entityType) {
                case 'empleado':
                    $this->rabbitmqService->publishEmpleadoEvent($data, $action);
                    break;
                case 'vehiculo':
                    $this->rabbitmqService->publishVehiculoEvent($data, $action);
                    break;
                case 'sucursal':
                    $this->rabbitmqService->publishSucursalEvent($data, $action);
                    break;
                case 'usuario':
                    $this->rabbitmqService->publishUsuarioEvent($data, $action);
                    break;
                case 'vehiculo_sucursal':
                    $this->rabbitmqService->publishAsignacionEvent($data, $action);
                    break;
            }

            return response()->json([
                'status' => 'success',
                'message' => "📤 Evento de {$entityType} publicado exitosamente",
                'data' => [
                    'entity_type' => $entityType,
                    'action' => $action,
                    'payload' => $data,
                    'queue' => "queue.{$entityType}s",
                    'exchange' => "microservice.{$entityType}s",
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error publicando evento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de colas
     */
    public function getQueueStats(): JsonResponse
    {
        try {
            $stats = $this->rabbitmqService->getQueueStats();
            $serviceStats = MicroserviceEventService::getQueueStats();

            return response()->json([
                'status' => 'success',
                'message' => '📊 Estadísticas de colas obtenidas',
                'data' => [
                    'rabbitmq_stats' => $stats,
                    'service_stats' => $serviceStats,
                    'total_queues' => count($stats),
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error obteniendo estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar múltiples eventos en lote
     */
    public function publishBatchEvents(Request $request): JsonResponse
    {
        $request->validate([
            'events' => 'required|array|min:1',
            'events.*.entity_type' => 'required|string',
            'events.*.action' => 'required|string',
            'events.*.data' => 'required|array'
        ]);

        try {
            $events = $request->input('events');
            $results = [];

            foreach ($events as $event) {
                $entityType = $event['entity_type'];
                $action = $event['action'];
                $data = $event['data'];

                switch ($entityType) {
                    case 'empleado':
                        $this->rabbitmqService->publishEmpleadoEvent($data, $action);
                        break;
                    case 'vehiculo':
                        $this->rabbitmqService->publishVehiculoEvent($data, $action);
                        break;
                    case 'sucursal':
                        $this->rabbitmqService->publishSucursalEvent($data, $action);
                        break;
                    case 'usuario':
                        $this->rabbitmqService->publishUsuarioEvent($data, $action);
                        break;
                    case 'vehiculo_sucursal':
                        $this->rabbitmqService->publishAsignacionEvent($data, $action);
                        break;
                }

                $results[] = [
                    'entity_type' => $entityType,
                    'action' => $action,
                    'status' => 'published'
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => "📦 {count($events)} eventos publicados en lote",
                'data' => [
                    'total_events' => count($events),
                    'results' => $results,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error publicando eventos en lote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test completo del sistema
     */
    public function fullSystemTest(): JsonResponse
    {
        try {
            $results = [];

            // 1. Health check
            $health = $this->rabbitmqService->healthCheck();
            $results['health_check'] = $health;

            // 2. Publicar eventos de prueba
            $testEvents = [
                ['type' => 'empleado', 'data' => ['id' => 'TEST-EMP-001', 'nombre' => 'Test Employee']],
                ['type' => 'vehiculo', 'data' => ['id' => 'TEST-VEH-001', 'placa' => 'TEST-001']],
                ['type' => 'sucursal', 'data' => ['id' => 'TEST-SUC-001', 'nombre' => 'Test Branch']]
            ];

            foreach ($testEvents as $event) {
                switch ($event['type']) {
                    case 'empleado':
                        $this->rabbitmqService->publishEmpleadoEvent($event['data'], 'created');
                        break;
                    case 'vehiculo':
                        $this->rabbitmqService->publishVehiculoEvent($event['data'], 'created');
                        break;
                    case 'sucursal':
                        $this->rabbitmqService->publishSucursalEvent($event['data'], 'created');
                        break;
                }
                $results['published_events'][] = $event['type'];
            }

            // 3. Obtener estadísticas
            $stats = $this->rabbitmqService->getQueueStats();
            $results['queue_stats'] = $stats;

            return response()->json([
                'status' => 'success',
                'message' => '🎯 Test completo del sistema ejecutado',
                'data' => $results,
                'summary' => [
                    'rabbitmq_status' => $health['status'],
                    'events_published' => count($testEvents),
                    'queues_monitored' => count($stats),
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error en test completo del sistema',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
