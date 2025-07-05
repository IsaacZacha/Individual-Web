<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GraphQLWebSocketService;

class QuickTestCommand extends Command
{
    protected $signature = 'test:quick';
    protected $description = 'Test rápido del sistema para verificar funcionamiento';

    protected GraphQLWebSocketService $service;

    public function __construct(GraphQLWebSocketService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->info("🚀 TEST RÁPIDO DEL SISTEMA");
        $this->line("═══════════════════════════════════════");

        try {
            // Test GraphQL
            $stats = $this->service->getEstadisticas();
            $this->line("✅ GraphQL: {$stats['totalEmpleados']} empleados, {$stats['totalVehiculos']} vehículos");

            // Test WebSocket
            $this->service->sendTestWebSocketEvent();
            $this->line("✅ WebSocket: Evento enviado correctamente");

            // Test sistema
            $systemStatus = $this->service->getSystemStatus();
            $graphqlStatus = $systemStatus['graphql']['status'] ?? 'unknown';
            $dbStatus = $systemStatus['database']['status'] ?? 'unknown';
            $this->line("✅ Sistema: GraphQL {$graphqlStatus}, DB {$dbStatus}");

            $this->newLine();
            $this->info("🎯 Todo funcionando correctamente!");
            $this->line("🌐 Dashboard: http://localhost:8000/dashboard");
            $this->line("📊 GraphQL: http://localhost:8000/graphiql");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
