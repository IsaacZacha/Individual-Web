<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GraphQLWebSocketService;
use App\Events\TestEvent;

class DashboardController extends Controller
{
    protected $graphqlWebSocketService;

    public function __construct(GraphQLWebSocketService $graphqlWebSocketService)
    {
        $this->graphqlWebSocketService = $graphqlWebSocketService;
    }

    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Obtener estadísticas generales usando el servicio GraphQL
     */
    public function estadisticas()
    {
        $estadisticas = $this->graphqlWebSocketService->getEstadisticas();
        return response()->json($estadisticas);
    }

    /**
     * Test de conexión WebSocket usando el servicio
     */
    public function testWebSocket()
    {
        $success = $this->graphqlWebSocketService->sendTestWebSocketEvent();

        return response()->json([
            'success' => $success,
            'message' => $success ? 
                'Evento de prueba enviado correctamente' : 
                'Error al enviar evento de prueba'
        ]);
    }

    /**
     * Estado del sistema usando el servicio integrado
     */
    public function status()
    {
        $status = $this->graphqlWebSocketService->getSystemStatus();
        return response()->json($status);
    }
}
