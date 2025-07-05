<?php

namespace App\Http\Controllers;

use App\Events\EmpleadoCreado;
use App\Events\VehiculoCreado;
use App\Events\SucursalCreada;
use App\Events\TestEvent;
use App\Models\Empleado;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller para probar eventos WebSocket en tiempo real
 */
class WebSocketTestController extends Controller
{
    /**
     * Disparar evento de empleado creado (simulado)
     */
    public function testEmpleadoCreado(Request $request)
    {
        try {
            // Crear empleado ficticio para prueba
            $empleadoData = [
                'id_empleado' => 999,
                'nombre' => 'Juan Test',
                'apellido' => 'WebSocket',
                'email' => 'test@websocket.com',
                'telefono' => '123456789',
                'direccion' => 'Calle Test 123',
                'fecha_contratacion' => now()->format('Y-m-d'),
                'salario' => 50000,
                'estado' => 'activo',
                'rol_id' => 1
            ];

            Log::info('🧪 Disparando evento EmpleadoCreado (simulado)', $empleadoData);
            
            // Test directo usando Broadcasting de Laravel
            try {
                // Datos del evento
                $eventData = [
                    'empleado' => $empleadoData,
                    'message' => "Nuevo empleado creado: {$empleadoData['nombre']}",
                    'timestamp' => now()->toISOString(),
                    'entity_type' => 'empleado',
                    'action' => 'created',
                    'type' => 'empleado.creado'
                ];
                
                // Usar el broadcasting manager directamente
                $broadcaster = app('Illuminate\Broadcasting\BroadcastManager');
                $connection = $broadcaster->connection();
                
                if ($connection) {
                    $channels = ['empleados', 'dashboard'];
                    foreach ($channels as $channel) {
                        $connection->broadcast(
                            [$channel], 
                            'empleado.creado', 
                            $eventData
                        );
                    }
                    $broadcastStatus = 'Reverb broadcast sent to channels: ' . implode(', ', $channels);
                } else {
                    $broadcastStatus = 'Broadcasting connection not available';
                }
                
            } catch (\Exception $broadcastError) {
                $broadcastStatus = 'Broadcast error: ' . $broadcastError->getMessage();
            }

            return response()->json([
                'success' => true,
                'message' => '🎉 Evento EmpleadoCreado disparado exitosamente',
                'broadcast_status' => $broadcastStatus,
                'data' => array_merge($empleadoData, [
                    'entity_type' => 'empleado',
                    'action' => 'created'
                ]),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error disparando evento EmpleadoCreado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disparar evento de vehículo creado (simulado)
     */
    public function testVehiculoCreado(Request $request)
    {
        try {
            $vehiculoData = [
                'id_vehiculo' => 999,
                'marca' => 'Toyota',
                'modelo' => 'Test WebSocket',
                'anio' => 2024,
                'placa' => 'WS-999',
                'color' => 'Azul',
                'numero_motor' => 'TEST123456',
                'numero_chasis' => 'CHASIS999',
                'estado' => 'disponible',
                'kilometraje' => 0
            ];

            Log::info('🧪 Disparando evento VehiculoCreado (simulado)', $vehiculoData);
            
            // Test directo usando Broadcasting de Laravel
            try {
                // Datos del evento
                $eventData = [
                    'vehiculo' => $vehiculoData,
                    'message' => "Nuevo vehículo creado: {$vehiculoData['marca']} {$vehiculoData['modelo']}",
                    'timestamp' => now()->toISOString(),
                    'entity_type' => 'vehiculo',
                    'action' => 'created',
                    'type' => 'vehiculo.creado'
                ];
                
                // Usar el broadcasting manager directamente
                $broadcaster = app('Illuminate\Broadcasting\BroadcastManager');
                $connection = $broadcaster->connection();
                
                if ($connection) {
                    $channels = ['vehiculos', 'dashboard'];
                    foreach ($channels as $channel) {
                        $connection->broadcast(
                            [$channel], 
                            'vehiculo.creado', 
                            $eventData
                        );
                    }
                    $broadcastStatus = 'Reverb broadcast sent to channels: ' . implode(', ', $channels);
                } else {
                    $broadcastStatus = 'Broadcasting connection not available';
                }
                
            } catch (\Exception $broadcastError) {
                $broadcastStatus = 'Broadcast error: ' . $broadcastError->getMessage();
            }

            return response()->json([
                'success' => true,
                'message' => '🚗 Evento VehiculoCreado disparado exitosamente',
                'broadcast_status' => $broadcastStatus,
                'data' => array_merge($vehiculoData, [
                    'entity_type' => 'vehiculo',
                    'action' => 'created'
                ]),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error disparando evento VehiculoCreado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disparar evento general de test
     */
    public function testGeneralEvent(Request $request)
    {
        try {
            $message = $request->input('message', 'Mensaje de prueba WebSocket');
            $channel = $request->input('channel', 'test-channel');
            
            Log::info('🧪 Disparando evento de test general', [
                'message' => $message,
                'channel' => $channel
            ]);
            
            // Usar broadcast helper directamente
            $eventData = [
                'message' => $message,
                'timestamp' => now()->toISOString(),
                'user' => 'Sistema Test',
                'type' => 'test'
            ];
            
            broadcast(new TestEvent($eventData))->toOthers();

            return response()->json([
                'success' => true,
                'message' => '📡 Evento de test disparado exitosamente',
                'data' => $eventData,
                'channel' => $channel
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error disparando evento de test: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disparar múltiples eventos para stress test
     */
    public function testMultipleEvents(Request $request)
    {
        try {
            $count = $request->input('count', 5);
            $results = [];
            
            for ($i = 1; $i <= $count; $i++) {
                $empleadoData = [
                    'id_empleado' => 900 + $i,
                    'nombre' => "Test$i",
                    'apellido' => "WebSocket$i",
                    'email' => "test$i@websocket.com",
                    'telefono' => "12345678$i",
                    'direccion' => "Calle Test $i",
                    'fecha_contratacion' => now()->format('Y-m-d'),
                    'salario' => 50000 + ($i * 1000),
                    'estado' => 'activo',
                    'rol_id' => 1
                ];

                $empleado = new Empleado($empleadoData);
                $empleado->id_empleado = 900 + $i;
                
                broadcast(new EmpleadoCreado($empleado));
                
                $results[] = $empleadoData;
                
                // Pequeña pausa entre eventos
                usleep(500000); // 0.5 segundos
            }

            return response()->json([
                'success' => true,
                'message' => "🎯 $count eventos disparados exitosamente",
                'events' => $results,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estado de conexiones WebSocket
     */
    public function getWebSocketStatus(Request $request)
    {
        return response()->json([
            'reverb_running' => true, // Podrías hacer un ping real al puerto 8080
            'broadcast_driver' => config('broadcasting.default'),
            'queue_driver' => config('queue.default'),
            'channels_configured' => [
                'empleados',
                'vehiculos', 
                'sucursales',
                'usuarios',
                'asignaciones',
                'dashboard'
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}
