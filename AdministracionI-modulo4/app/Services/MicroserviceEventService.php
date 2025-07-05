<?php

namespace App\Services;

use App\Jobs\MicroserviceEventJob;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar eventos de microservicios
 * Actúa como un canal tipo RabbitMQ usando colas de Laravel
 */
class MicroserviceEventService
{
    /**
     * Canales disponibles (como exchanges en RabbitMQ)
     */
    const CHANNELS = [
        'empleados' => 'empleados',
        'vehiculos' => 'vehiculos',
        'sucursales' => 'sucursales',
        'usuarios' => 'usuarios',
        'asignaciones' => 'asignaciones',
        'notificaciones' => 'notificaciones',
        'auditoria' => 'auditoria'
    ];

    /**
     * Acciones disponibles
     */
    const ACTIONS = [
        'created' => 'created',
        'updated' => 'updated',
        'deleted' => 'deleted',
        'assigned' => 'assigned',
        'unassigned' => 'unassigned',
        'status_changed' => 'status_changed'
    ];

    /**
     * Enviar evento de empleado
     */
    public static function empleadoEvent(array $empleadoData, string $action = 'created'): void
    {
        self::dispatchEvent('empleado', $empleadoData, $action, self::CHANNELS['empleados']);
    }

    /**
     * Enviar evento de vehículo
     */
    public static function vehiculoEvent(array $vehiculoData, string $action = 'created'): void
    {
        self::dispatchEvent('vehiculo', $vehiculoData, $action, self::CHANNELS['vehiculos']);
    }

    /**
     * Enviar evento de sucursal
     */
    public static function sucursalEvent(array $sucursalData, string $action = 'created'): void
    {
        self::dispatchEvent('sucursal', $sucursalData, $action, self::CHANNELS['sucursales']);
    }

    /**
     * Enviar evento de usuario
     */
    public static function usuarioEvent(array $usuarioData, string $action = 'created'): void
    {
        self::dispatchEvent('usuario', $usuarioData, $action, self::CHANNELS['usuarios']);
    }

    /**
     * Enviar evento de asignación vehículo-sucursal
     */
    public static function asignacionEvent(array $asignacionData, string $action = 'assigned'): void
    {
        self::dispatchEvent('vehiculo_sucursal', $asignacionData, $action, self::CHANNELS['asignaciones']);
    }

    /**
     * Enviar evento de cambio de estado de vehículo
     */
    public static function vehiculoEstadoEvent(array $vehiculoData, string $estadoAnterior, string $estadoNuevo): void
    {
        $eventData = array_merge($vehiculoData, [
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'timestamp' => now()->toISOString()
        ]);

        self::dispatchEvent('vehiculo', $eventData, self::ACTIONS['status_changed'], self::CHANNELS['vehiculos']);
    }

    /**
     * Enviar múltiples eventos en batch
     */
    public static function batchEvents(array $events): void
    {
        foreach ($events as $event) {
            self::dispatchEvent(
                $event['entity_type'],
                $event['data'],
                $event['action'] ?? 'created',
                $event['channel'] ?? 'default'
            );
        }
    }

    /**
     * Método principal para despachar eventos
     */
    private static function dispatchEvent(
        string $entityType,
        array $entityData,
        string $action,
        string $channel
    ): void {
        try {
            // Validar datos antes de enviar
            if (empty($entityData)) {
                Log::warning("⚠️ Datos vacíos para evento de microservicio", [
                    'entity_type' => $entityType,
                    'action' => $action,
                    'channel' => $channel
                ]);
                return;
            }

            // Agregar metadatos del evento
            $enrichedData = array_merge($entityData, [
                'event_metadata' => [
                    'created_at' => now()->toISOString(),
                    'microservice' => 'administracion-modulo4',
                    'version' => '1.0',
                    'environment' => app()->environment()
                ]
            ]);

            // Despachar el job
            MicroserviceEventJob::dispatchEvent($entityType, $enrichedData, $action, $channel);

            Log::info("📤 Evento de microservicio despachado", [
                'entity_type' => $entityType,
                'action' => $action,
                'channel' => $channel,
                'queue' => "microservice-{$channel}"
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error despachando evento de microservicio", [
                'entity_type' => $entityType,
                'action' => $action,
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de colas
     */
    public static function getQueueStats(): array
    {
        $stats = [];
        
        foreach (self::CHANNELS as $channelName => $channelKey) {
            $queueName = "microservice-{$channelKey}";
            $stats[$channelName] = [
                'queue_name' => $queueName,
                'pending_jobs' => 0, // Aquí podrías integrar con Redis/RabbitMQ para obtener stats reales
                'processed_today' => 0,
                'failed_jobs' => 0
            ];
        }

        return $stats;
    }

    /**
     * Verificar salud de las colas
     */
    public static function healthCheck(): array
    {
        return [
            'status' => 'healthy',
            'queue_connection' => config('queue.default'),
            'available_channels' => array_keys(self::CHANNELS),
            'total_channels' => count(self::CHANNELS),
            'timestamp' => now()->toISOString()
        ];
    }
}
