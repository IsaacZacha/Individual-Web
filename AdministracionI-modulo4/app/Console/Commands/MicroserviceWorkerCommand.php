<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MicroserviceWorkerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microservice:work
                            {channel? : El canal específico a procesar}
                            {--all : Procesar todos los canales}
                            {--timeout=60 : Timeout en segundos}
                            {--memory=512 : Límite de memoria en MB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Iniciar workers para procesar eventos de microservicios (simulando RabbitMQ)';

    /**
     * Canales disponibles
     */
    protected $channels = [
        'empleados',
        'vehiculos', 
        'sucursales',
        'usuarios',
        'asignaciones',
        'notificaciones',
        'auditoria'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Iniciando workers de microservicios...");

        $channel = $this->argument('channel');
        $processAll = $this->option('all');
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');

        if ($processAll) {
            $this->info("📡 Procesando TODOS los canales de microservicios");
            $this->startAllChannelWorkers($timeout, $memory);
        } elseif ($channel) {
            if (!in_array($channel, $this->channels)) {
                $this->error("❌ Canal '{$channel}' no existe");
                $this->info("Canales disponibles: " . implode(', ', $this->channels));
                return 1;
            }
            $this->info("📡 Procesando canal específico: {$channel}");
            $this->startChannelWorker($channel, $timeout, $memory);
        } else {
            $this->showChannelMenu();
        }

        return 0;
    }

    /**
     * Mostrar menú de selección de canal
     */
    protected function showChannelMenu(): void
    {
        $this->info("🎯 Selecciona un canal para procesar:");
        
        $choices = array_merge($this->channels, ['all' => 'Todos los canales']);
        $selected = $this->choice('¿Qué canal quieres procesar?', $choices);

        if ($selected === 'Todos los canales') {
            $this->startAllChannelWorkers();
        } else {
            $this->startChannelWorker($selected);
        }
    }

    /**
     * Iniciar worker para un canal específico
     */
    protected function startChannelWorker(string $channel, int $timeout = 60, int $memory = 512): void
    {
        $queueName = "microservice-{$channel}";
        
        $this->info("🎯 Iniciando worker para canal: {$channel}");
        $this->info("📋 Cola: {$queueName}");
        $this->line("⏱️  Timeout: {$timeout}s | 💾 Memoria: {$memory}MB");
        $this->newLine();

        // Ejecutar el worker
        $exitCode = Artisan::call('queue:work', [
            '--queue' => $queueName,
            '--timeout' => $timeout,
            '--memory' => $memory,
            '--tries' => 3,
            '--delay' => 3,
            '--verbose' => true
        ]);

        if ($exitCode === 0) {
            $this->info("✅ Worker para canal '{$channel}' finalizado correctamente");
        } else {
            $this->error("❌ Worker para canal '{$channel}' terminó con errores");
        }
    }

    /**
     * Iniciar workers para todos los canales
     */
    protected function startAllChannelWorkers(int $timeout = 60, int $memory = 512): void
    {
        $this->info("🌐 Iniciando workers para todos los canales...");
        
        foreach ($this->channels as $channel) {
            $this->info("🔄 Procesando canal: {$channel}");
            
            // En un entorno real, podrías usar procesos en paralelo
            // Por ahora, procesamos secuencialmente
            $this->startChannelWorker($channel, $timeout, $memory);
        }
        
        $this->info("✅ Todos los workers han sido procesados");
    }
}
