<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para enviar notificaciones al WebSocket Service
 * Integra el módulo Laravel con el sistema de notificaciones en tiempo real
 */
class NotificationService
{
    private string $websocketUrl;
    private int $timeout;
    private bool $enabled;

    public function __construct()
    {
        $this->websocketUrl = config('services.websocket.url', 'http://localhost:3001');
        $this->timeout = config('services.websocket.timeout', 3);
        $this->enabled = config('services.websocket.enabled', true);
    }

    /**
     * Envía una notificación al servicio WebSocket
     */
    public function notify(string $event, array $data, string $source = 'laravel'): bool
    {
        if (!$this->enabled) {
            Log::info('WebSocket notifications disabled');
            return false;
        }

        try {
            $payload = [
                'event' => $event,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'source' => $source
            ];

            Log::info('Sending notification to WebSocket', [
                'event' => $event,
                'websocket_url' => $this->websocketUrl
            ]);

            $response = Http::timeout($this->timeout)
                ->post("{$this->websocketUrl}/notify", $payload);

            if ($response->successful()) {
                Log::info('Notification sent successfully', [
                    'event' => $event,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::warning('Failed to send notification', [
                    'event' => $event,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error sending notification to WebSocket', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notifica la creación de un empleado
     */
    public function empleadoCreado(array $empleado): bool
    {
        return $this->notify('empleado_creado', [
            'id_empleado' => $empleado['id_empleado'] ?? null,
            'nombre' => $empleado['nombre'] ?? '',
            'cargo' => $empleado['cargo'] ?? '',
            'correo' => $empleado['correo'] ?? '',
            'telefono' => $empleado['telefono'] ?? '',
            'message' => "Nuevo empleado registrado: {$empleado['nombre']}"
        ]);
    }

    /**
     * Notifica la actualización de un empleado
     */
    public function empleadoActualizado(array $empleado, array $cambios = []): bool
    {
        return $this->notify('empleado_actualizado', [
            'id_empleado' => $empleado['id_empleado'] ?? null,
            'nombre' => $empleado['nombre'] ?? '',
            'cargo' => $empleado['cargo'] ?? '',
            'cambios' => $cambios,
            'message' => "Empleado actualizado: {$empleado['nombre']}"
        ]);
    }

    /**
     * Notifica la eliminación de un empleado
     */
    public function empleadoEliminado(int $empleadoId, string $nombre = ''): bool
    {
        return $this->notify('empleado_eliminado', [
            'id_empleado' => $empleadoId,
            'nombre' => $nombre,
            'message' => "Empleado eliminado: {$nombre}"
        ]);
    }

    /**
     * Notifica la creación de un vehículo
     */
    public function vehiculoCreado(array $vehiculo): bool
    {
        return $this->notify('vehiculo_creado', [
            'id_vehiculo' => $vehiculo['id_vehiculo'] ?? null,
            'placa' => $vehiculo['placa'] ?? '',
            'marca' => $vehiculo['marca'] ?? '',
            'modelo' => $vehiculo['modelo'] ?? '',
            'estado' => $vehiculo['estado'] ?? '',
            'message' => "Nuevo vehículo registrado: {$vehiculo['placa']}"
        ]);
    }

    /**
     * Notifica la actualización de un vehículo
     */
    public function vehiculoActualizado(array $vehiculo, array $cambios = []): bool
    {
        return $this->notify('vehiculo_actualizado', [
            'id_vehiculo' => $vehiculo['id_vehiculo'] ?? null,
            'placa' => $vehiculo['placa'] ?? '',
            'marca' => $vehiculo['marca'] ?? '',
            'modelo' => $vehiculo['modelo'] ?? '',
            'estado' => $vehiculo['estado'] ?? '',
            'cambios' => $cambios,
            'message' => "Vehículo actualizado: {$vehiculo['placa']}"
        ]);
    }

    /**
     * Notifica el cambio de estado de un vehículo (evento crítico)
     */
    public function vehiculoEstadoCambiado(array $vehiculo, string $estadoAnterior, string $estadoNuevo): bool
    {
        $esCritico = in_array($estadoNuevo, ['En Mantenimiento', 'Fuera de Servicio', 'Accidentado']);
        
        return $this->notify('vehiculo_estado_cambiado', [
            'id_vehiculo' => $vehiculo['id_vehiculo'] ?? null,
            'placa' => $vehiculo['placa'] ?? '',
            'marca' => $vehiculo['marca'] ?? '',
            'modelo' => $vehiculo['modelo'] ?? '',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'es_critico' => $esCritico,
            'priority' => $esCritico ? 'high' : 'normal',
            'message' => "Vehículo {$vehiculo['placa']} cambió de estado: {$estadoAnterior} → {$estadoNuevo}"
        ]);
    }

    /**
     * Notifica la eliminación de un vehículo
     */
    public function vehiculoEliminado(int $vehiculoId, string $placa = ''): bool
    {
        return $this->notify('vehiculo_eliminado', [
            'id_vehiculo' => $vehiculoId,
            'placa' => $placa,
            'message' => "Vehículo eliminado: {$placa}"
        ]);
    }

    /**
     * Notifica la asignación de vehículo a sucursal
     */
    public function vehiculoAsignado(array $asignacion, array $vehiculo = [], array $sucursal = []): bool
    {
        return $this->notify('vehiculo_asignado', [
            'id_asignacion' => $asignacion['id'] ?? null,
            'id_vehiculo' => $asignacion['id_vehiculo'] ?? null,
            'id_sucursal' => $asignacion['id_sucursal'] ?? null,
            'fecha_asignacion' => $asignacion['fecha_asignacion'] ?? null,
            'vehiculo' => [
                'placa' => $vehiculo['placa'] ?? '',
                'marca' => $vehiculo['marca'] ?? '',
                'modelo' => $vehiculo['modelo'] ?? ''
            ],
            'sucursal' => [
                'nombre' => $sucursal['nombre'] ?? '',
                'ciudad' => $sucursal['ciudad'] ?? ''
            ],
            'message' => "Vehículo {$vehiculo['placa']} asignado a {$sucursal['nombre']}"
        ]);
    }

    /**
     * Notifica la desasignación de vehículo de sucursal
     */
    public function vehiculoDesasignado(int $asignacionId, array $vehiculo = [], array $sucursal = []): bool
    {
        return $this->notify('vehiculo_desasignado', [
            'id_asignacion' => $asignacionId,
            'vehiculo' => [
                'placa' => $vehiculo['placa'] ?? '',
                'marca' => $vehiculo['marca'] ?? '',
                'modelo' => $vehiculo['modelo'] ?? ''
            ],
            'sucursal' => [
                'nombre' => $sucursal['nombre'] ?? '',
                'ciudad' => $sucursal['ciudad'] ?? ''
            ],
            'message' => "Vehículo {$vehiculo['placa']} desasignado de {$sucursal['nombre']}"
        ]);
    }

    /**
     * Notifica la creación de una sucursal
     */
    public function sucursalCreada(array $sucursal): bool
    {
        return $this->notify('sucursal_creada', [
            'id_sucursal' => $sucursal['id_sucursal'] ?? null,
            'nombre' => $sucursal['nombre'] ?? '',
            'direccion' => $sucursal['direccion'] ?? '',
            'ciudad' => $sucursal['ciudad'] ?? '',
            'telefono' => $sucursal['telefono'] ?? '',
            'message' => "Nueva sucursal creada: {$sucursal['nombre']} en {$sucursal['ciudad']}"
        ]);
    }

    /**
     * Notifica la actualización de una sucursal
     */
    public function sucursalActualizada(array $sucursal, array $cambios = []): bool
    {
        return $this->notify('sucursal_actualizada', [
            'id_sucursal' => $sucursal['id_sucursal'] ?? null,
            'nombre' => $sucursal['nombre'] ?? '',
            'direccion' => $sucursal['direccion'] ?? '',
            'ciudad' => $sucursal['ciudad'] ?? '',
            'telefono' => $sucursal['telefono'] ?? '',
            'cambios' => $cambios,
            'message' => "Sucursal actualizada: {$sucursal['nombre']}"
        ]);
    }

    /**
     * Notifica la eliminación de una sucursal
     */
    public function sucursalEliminada(int $sucursalId, string $nombre = ''): bool
    {
        return $this->notify('sucursal_eliminada', [
            'id_sucursal' => $sucursalId,
            'nombre' => $nombre,
            'message' => "Sucursal eliminada: {$nombre}"
        ]);
    }

    /**
     * Verifica si el servicio WebSocket está disponible
     */
    public function isWebSocketServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->websocketUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene estadísticas del servicio WebSocket
     */
    public function getWebSocketStats(): ?array
    {
        try {
            $response = Http::timeout(3)->get("{$this->websocketUrl}/stats");
            
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error getting WebSocket stats', ['error' => $e->getMessage()]);
        }
        
        return null;
    }
}
