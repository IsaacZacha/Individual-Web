<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Empleado;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\User;
use App\Events\TestEvent;

/**
 * Servicio para operaciones GraphQL y WebSocket integradas
 * Maneja la lógica de negocio y eventos en tiempo real
 */
class GraphQLWebSocketService
{
    /**
     * Obtener estadísticas completas del sistema
     */
    public function getEstadisticas(): array
    {
        $estadisticas = [
            'totalEmpleados' => Empleado::count(),
            'totalVehiculos' => Vehiculo::count(),
            'totalSucursales' => Sucursal::count(),
            'totalUsuarios' => User::count(),
            'vehiculosPorEstado' => $this->getVehiculosPorEstado(),
            'empleadosConUsuarios' => $this->getEmpleadosConUsuarios(),
            'sucursalesConVehiculos' => $this->getSucursalesConVehiculos(),
            'timestamp' => now()->toISOString()
        ];

        return $estadisticas;
    }

    /**
     * Obtener vehículos agrupados por estado
     */
    public function getVehiculosPorEstado(): array
    {
        return Vehiculo::selectRaw('estado, count(*) as count')
            ->groupBy('estado')
            ->get()
            ->map(function ($item) {
                return [
                    'estado' => $item->estado,
                    'count' => $item->count
                ];
            })
            ->toArray();
    }

    /**
     * Obtener empleados con sus usuarios
     */
    public function getEmpleadosConUsuarios(): array
    {
        return Empleado::with(['user.rol'])
            ->get()
            ->map(function ($empleado) {
                return [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre,
                    'cargo' => $empleado->cargo,
                    'correo' => $empleado->correo,
                    'user' => $empleado->user ? [
                        'id' => $empleado->user->id_usuario,
                        'name' => $empleado->user->name,
                        'email' => $empleado->user->email,
                        'rol' => $empleado->user->rol ? [
                            'id_rol' => $empleado->user->rol->id_rol,
                            'nombre' => $empleado->user->rol->nombre,
                            'descripcion' => $empleado->user->rol->descripcion
                        ] : null
                    ] : null
                ];
            })
            ->toArray();
    }

    /**
     * Obtener sucursales con vehículos asignados
     */
    public function getSucursalesConVehiculos(): array
    {
        return Sucursal::with(['vehiculos.vehiculo'])
            ->get()
            ->map(function ($sucursal) {
                return [
                    'id_sucursal' => $sucursal->id_sucursal,
                    'nombre' => $sucursal->nombre,
                    'direccion' => $sucursal->direccion,
                    'ciudad' => $sucursal->ciudad,
                    'telefono' => $sucursal->telefono,
                    'vehiculos' => $sucursal->vehiculos->map(function ($asignacion) {
                        return [
                            'id' => $asignacion->id,
                            'fecha_asignacion' => $asignacion->fecha_asignacion,
                            'vehiculo' => $asignacion->vehiculo ? [
                                'id_vehiculo' => $asignacion->vehiculo->id_vehiculo,
                                'placa' => $asignacion->vehiculo->placa,
                                'marca' => $asignacion->vehiculo->marca,
                                'modelo' => $asignacion->vehiculo->modelo,
                                'estado' => $asignacion->vehiculo->estado
                            ] : null
                        ];
                    })->toArray()
                ];
            })
            ->toArray();
    }

    /**
     * Enviar evento de prueba WebSocket
     */
    public function sendTestWebSocketEvent(): bool
    {
        try {
            $testData = [
                'message' => 'Evento de prueba desde GraphQLWebSocketService',
                'timestamp' => now()->toISOString(),
                'estadisticas' => $this->getEstadisticas(),
                'tipo' => 'test_event'
            ];

            // Emitir evento usando Laravel Broadcasting
            broadcast(new TestEvent($testData));

            Log::info('Test WebSocket event sent successfully', $testData);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send test WebSocket event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Verificar estado de todos los servicios
     */
    public function getSystemStatus(): array
    {
        return [
            'graphql' => $this->checkGraphQLStatus(),
            'websocket' => $this->checkWebSocketStatus(),
            'database' => $this->checkDatabaseStatus(),
            'reverb' => $this->checkReverbStatus(),
            'broadcasting' => $this->checkBroadcastingStatus(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Verificar estado GraphQL
     */
    private function checkGraphQLStatus(): array
    {
        try {
            $schemaPath = base_path('graphql/schema.graphql');
            $configExists = config('lighthouse.route.uri') !== null;
            
            return [
                'status' => (file_exists($schemaPath) && $configExists) ? 'online' : 'offline',
                'schema_exists' => file_exists($schemaPath),
                'config_exists' => $configExists,
                'endpoint' => config('lighthouse.route.uri', '/graphql'),
                'schema_path' => $schemaPath
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar estado WebSocket/Broadcasting
     */
    private function checkWebSocketStatus(): array
    {
        try {
            $driver = config('broadcasting.default');
            $reverbConfig = config('broadcasting.connections.reverb');
            
            return [
                'status' => $driver === 'reverb' ? 'configured' : 'not_configured',
                'driver' => $driver,
                'reverb_host' => $reverbConfig['host'] ?? 'unknown',
                'reverb_port' => $reverbConfig['port'] ?? 'unknown',
                'app_key' => $reverbConfig['app_key'] ?? 'unknown'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar estado de la base de datos
     */
    private function checkDatabaseStatus(): array
    {
        try {
            \DB::connection()->getPdo();
            return [
                'status' => 'online',
                'connection' => config('database.default'),
                'database' => config('database.connections.' . config('database.default') . '.database')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar estado del servidor Reverb
     */
    private function checkReverbStatus(): array
    {
        try {
            $host = config('broadcasting.connections.reverb.host', 'localhost');
            $port = config('broadcasting.connections.reverb.port', 8080);
            
            $connection = @fsockopen($host, $port, $errno, $errstr, 1);
            
            if ($connection) {
                fclose($connection);
                return [
                    'status' => 'online',
                    'host' => $host,
                    'port' => $port
                ];
            } else {
                return [
                    'status' => 'offline',
                    'host' => $host,
                    'port' => $port,
                    'message' => 'Servidor Reverb no disponible'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar configuración de broadcasting
     */
    private function checkBroadcastingStatus(): array
    {
        try {
            $broadcastDriver = config('broadcasting.default');
            $pusherConfig = config('broadcasting.connections.pusher');
            
            return [
                'status' => 'configured',
                'default_driver' => $broadcastDriver,
                'pusher_configured' => $pusherConfig !== null,
                'reverb_configured' => config('broadcasting.connections.reverb') !== null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}
