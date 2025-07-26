<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador simplificado para probar funcionalidad básica
 */

class SimpleTestController extends Controller
{
    /**
     * Test simple que solo devuelve datos sin broadcasting
     */
    public function testSimple(Request $request)
    {
        try {
            $data = [
                'id' => 999,
                'nombre' => 'Test Simple',
                'timestamp' => now()->toISOString(),
                'message' => 'Test exitoso sin broadcasting'
            ];

            Log::info('🧪 Test simple ejecutado', $data);

            return response()->json([
                'success' => true,
                'message' => '✅ Test simple exitoso',
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error en test simple: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test que simula datos de actividad para el dashboard
     */
    public function testActivity(Request $request)
    {
        try {
            $activities = [
                [
                    'type' => 'empleado.creado',
                    'message' => 'Se creó empleado Juan Test',
                    'timestamp' => now()->subMinutes(5)->toISOString(),
                    'icon' => 'user'
                ],
                [
                    'type' => 'vehiculo.creado',
                    'message' => 'Se agregó vehículo Toyota Test',
                    'timestamp' => now()->subMinutes(10)->toISOString(),
                    'icon' => 'car'
                ],
                [
                    'type' => 'sucursal.creada',
                    'message' => 'Se registró sucursal Test Center',
                    'timestamp' => now()->subMinutes(15)->toISOString(),
                    'icon' => 'building'
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => '📊 Datos de actividad simulados',
                'activities' => $activities,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
