<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job para manejar eventos de microservicios usando colas tipo RabbitMQ
 * Funciona tanto con Redis como con RabbitMQ Cloud
 */
class MicroserviceEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    public $backoff = [10, 30, 60]; // segundos entre reintentos

    protected $eventType;
    protected $entityType;
    protected $entityData;
    protected $action;
    protected $microserviceChannel;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $eventType,
        string $entityType,
        array $entityData,
        string $action = 'created',
        string $microserviceChannel = 'default'
    ) {
        $this->eventType = $eventType;
        $this->entityType = $entityType;
        $this->entityData = $entityData;
        $this->action = $action;
        $this->microserviceChannel = $microserviceChannel;

        // Configurar la cola específica para el canal
        $this->onQueue("microservice-{$microserviceChannel}");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("🚀 Procesando evento de microservicio", [
                'event_type' => $this->eventType,
                'entity_type' => $this->entityType,
                'action' => $this->action,
                'channel' => $this->microserviceChannel,
                'data' => $this->entityData
            ]);

            // Simular procesamiento del evento
            $this->processEvent();

            // Enviar a otros microservicios si es necesario
            $this->notifyMicroservices();

            Log::info("✅ Evento de microservicio procesado exitosamente", [
                'event_type' => $this->eventType,
                'entity_type' => $this->entityType,
                'job_id' => $this->job->getJobId()
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error procesando evento de microservicio", [
                'event_type' => $this->eventType,
                'entity_type' => $this->entityType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar la excepción para que Laravel maneje los reintentos
            throw $e;
        }
    }

    /**
     * Procesar el evento específico
     */
    private function processEvent(): void
    {
        switch ($this->entityType) {
            case 'empleado':
                $this->processEmpleadoEvent();
                break;
            case 'vehiculo':
                $this->processVehiculoEvent();
                break;
            case 'sucursal':
                $this->processSucursalEvent();
                break;
            case 'usuario':
                $this->processUsuarioEvent();
                break;
            case 'vehiculo_sucursal':
                $this->processAsignacionEvent();
                break;
            default:
                Log::warning("Tipo de entidad no reconocido: {$this->entityType}");
        }
    }

    /**
     * Eventos específicos por entidad
     */
    private function processEmpleadoEvent(): void
    {
        // Lógica específica para empleados
        Log::info("👤 Procesando evento de empleado", [
            'empleado_id' => $this->entityData['id_empleado'] ?? null,
            'nombre' => $this->entityData['nombre'] ?? null,
            'action' => $this->action
        ]);
    }

    private function processVehiculoEvent(): void
    {
        // Lógica específica para vehículos
        Log::info("🚗 Procesando evento de vehículo", [
            'vehiculo_id' => $this->entityData['id_vehiculo'] ?? null,
            'placa' => $this->entityData['placa'] ?? null,
            'estado' => $this->entityData['estado'] ?? null,
            'action' => $this->action
        ]);
    }

    private function processSucursalEvent(): void
    {
        // Lógica específica para sucursales
        Log::info("🏢 Procesando evento de sucursal", [
            'sucursal_id' => $this->entityData['id_sucursal'] ?? null,
            'nombre' => $this->entityData['nombre'] ?? null,
            'action' => $this->action
        ]);
    }

    private function processUsuarioEvent(): void
    {
        // Lógica específica para usuarios
        Log::info("👥 Procesando evento de usuario", [
            'user_id' => $this->entityData['id'] ?? null,
            'email' => $this->entityData['email'] ?? null,
            'action' => $this->action
        ]);
    }

    private function processAsignacionEvent(): void
    {
        // Lógica específica para asignaciones vehículo-sucursal
        Log::info("🔗 Procesando evento de asignación", [
            'vehiculo_id' => $this->entityData['id_vehiculo'] ?? null,
            'sucursal_id' => $this->entityData['id_sucursal'] ?? null,
            'fecha_asignacion' => $this->entityData['fecha_asignacion'] ?? null,
            'action' => $this->action
        ]);
    }

    /**
     * Notificar a otros microservicios
     */
    private function notifyMicroservices(): void
    {
        // Simular notificación a microservicios externos
        $payload = [
            'timestamp' => now()->toISOString(),
            'event_type' => $this->eventType,
            'entity_type' => $this->entityType,
            'action' => $this->action,
            'data' => $this->entityData,
            'microservice' => 'administracion-modulo4',
            'version' => '1.0'
        ];

        // Aquí podrías hacer HTTP requests a otros microservicios
        // o enviar a sistemas de mensajería externos
        Log::info("📡 Notificando a microservicios externos", $payload);
    }

    /**
     * Manejar job fallido
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("💥 Job de microservicio falló completamente", [
            'event_type' => $this->eventType,
            'entity_type' => $this->entityType,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Dispatch estático para facilitar el uso
     */
    public static function dispatchEvent(
        string $entityType,
        array $entityData,
        string $action = 'created',
        string $channel = 'default'
    ): void {
        $eventType = "microservice.{$entityType}.{$action}";
        
        self::dispatch($eventType, $entityType, $entityData, $action, $channel)
            ->onQueue("microservice-{$channel}")
            ->delay(now()->addSeconds(1)); // Pequeño delay para evitar race conditions
    }
}
