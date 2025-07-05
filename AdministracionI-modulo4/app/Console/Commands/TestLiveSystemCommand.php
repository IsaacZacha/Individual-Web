<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GraphQLWebSocketService;
use App\Events\TestEvent;

class TestLiveSystemCommand extends Command
{
    protected $signature = 'test:live {--seconds=30 : Duración del test en segundos}';
    protected $description = 'Test en vivo del sistema enviando eventos WebSocket cada 5 segundos';

    protected GraphQLWebSocketService $service;

    public function __construct(GraphQLWebSocketService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $seconds = (int) $this->option('seconds');
        $this->info("🔴 INICIANDO TEST EN VIVO DEL SISTEMA");
        $this->line("📊 Enviando eventos WebSocket cada 5 segundos por {$seconds} segundos...");
        $this->line("🌐 Dashboard: http://localhost:8000/dashboard");
        $this->line("📡 WebSocket: ws://127.0.0.1:8080");
        $this->newLine();

        $startTime = time();
        $eventCount = 0;

        while ((time() - $startTime) < $seconds) {
            $eventCount++;
            
            // Enviar evento de test
            $this->service->sendTestWebSocketEvent();
            
            // Obtener estadísticas actuales
            $stats = $this->service->getEstadisticas();
            
            $this->line(sprintf(
                "📤 Evento #%d enviado - Empleados: %d, Vehículos: %d [%s]",
                $eventCount,
                $stats['empleados_total'] ?? 0,
                $stats['vehiculos_total'] ?? 0,
                now()->format('H:i:s')
            ));

            // Esperar 5 segundos
            sleep(5);
        }

        $this->newLine();
        $this->info("✅ Test completado - {$eventCount} eventos enviados");
        $this->line("📊 Revisa el dashboard para ver las actualizaciones en tiempo real");
        
        return 0;
    }
}
