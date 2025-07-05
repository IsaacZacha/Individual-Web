<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RabbitMQService;

class TestRabbitMQCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:test
                            {action=health : Acción a realizar (health|publish|consume)}
                            {--entity=empleado : Tipo de entidad (empleado|vehiculo|sucursal|usuario)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la conexión y funcionalidad de RabbitMQ Cloud (CloudAMQP)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        $this->info("🐰 Probando RabbitMQ CloudAMQP...");
        $this->info("📡 Host: shark.rmq.cloudamqp.com");
        $this->newLine();

        try {
            $rabbitmq = new RabbitMQService();

            switch ($action) {
                case 'health':
                    $this->testHealth($rabbitmq);
                    break;
                case 'publish':
                    $this->testPublish($rabbitmq);
                    break;
                case 'consume':
                    $this->testConsume($rabbitmq);
                    break;
                default:
                    $this->error("Acción no válida. Usa: health, publish, o consume");
                    return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Probar salud de la conexión
     */
    private function testHealth(RabbitMQService $rabbitmq): void
    {
        $this->info("🔍 Verificando salud de la conexión...");
        
        $health = $rabbitmq->healthCheck();
        
        if ($health['status'] === 'healthy') {
            $this->info("✅ Conexión saludable");
            $this->line("📊 Detalles:");
            $this->line("   - Host: {$health['host']}");
            $this->line("   - VHost: {$health['vhost']}");
            $this->line("   - SSL: " . ($health['ssl'] ? 'Habilitado' : 'Deshabilitado'));
            $this->line("   - Conexión: " . ($health['connection'] ? 'Activa' : 'Inactiva'));
            $this->line("   - Canal: " . ($health['channel'] ? 'Abierto' : 'Cerrado'));
        } else {
            $this->error("❌ Conexión no saludable");
            if (isset($health['error'])) {
                $this->error("Error: " . $health['error']);
            }
        }

        // Mostrar estadísticas de colas
        $this->newLine();
        $this->info("📋 Estadísticas de colas:");
        
        $stats = $rabbitmq->getQueueStats();
        $this->table(
            ['Entidad', 'Cola', 'Mensajes', 'Consumidores', 'Exchange'],
            collect($stats)->map(function ($stat, $entity) {
                return [
                    $entity,
                    $stat['queue_name'] ?? 'N/A',
                    $stat['message_count'] ?? 'Error',
                    $stat['consumer_count'] ?? 'Error',
                    $stat['exchange'] ?? 'N/A'
                ];
            })->values()->toArray()
        );
    }

    /**
     * Probar publicación de mensajes
     */
    private function testPublish(RabbitMQService $rabbitmq): void
    {
        $entityType = $this->option('entity');
        
        $this->info("📤 Probando publicación de mensaje...");
        $this->line("🎯 Entidad: {$entityType}");
        
        // Datos de prueba según el tipo de entidad
        $testData = $this->getTestData($entityType);
        
        switch ($entityType) {
            case 'empleado':
                $rabbitmq->publishEmpleadoEvent($testData, 'created');
                break;
            case 'vehiculo':
                $rabbitmq->publishVehiculoEvent($testData, 'created');
                break;
            case 'sucursal':
                $rabbitmq->publishSucursalEvent($testData, 'created');
                break;
            case 'usuario':
                $rabbitmq->publishUsuarioEvent($testData, 'created');
                break;
            default:
                $this->error("Tipo de entidad no soportado: {$entityType}");
                return;
        }
        
        $this->info("✅ Mensaje publicado exitosamente");
        $this->line("📊 Datos enviados:");
        $this->line(json_encode($testData, JSON_PRETTY_PRINT));
    }

    /**
     * Probar consumo de mensajes
     */
    private function testConsume(RabbitMQService $rabbitmq): void
    {
        $entityType = $this->option('entity');
        
        $this->info("👂 Iniciando consumo de mensajes...");
        $this->line("🎯 Entidad: {$entityType}");
        $this->line("⏱️  Timeout: 30 segundos");
        $this->newLine();
        
        $messageCount = 0;
        
        $rabbitmq->consumeQueue($entityType, function($data) use (&$messageCount) {
            $messageCount++;
            $this->info("📨 Mensaje #{$messageCount} recibido:");
            $this->line("   - Tipo: " . ($data['entity_type'] ?? 'unknown'));
            $this->line("   - Acción: " . ($data['action'] ?? 'unknown'));
            $this->line("   - Timestamp: " . ($data['timestamp'] ?? 'unknown'));
            $this->line("   - Correlation ID: " . ($data['correlation_id'] ?? 'unknown'));
            $this->newLine();
        });
        
        if ($messageCount === 0) {
            $this->warn("⚠️  No se recibieron mensajes en el timeout especificado");
        } else {
            $this->info("✅ Procesados {$messageCount} mensajes");
        }
    }

    /**
     * Obtener datos de prueba según el tipo de entidad
     */
    private function getTestData(string $entityType): array
    {
        switch ($entityType) {
            case 'empleado':
                return [
                    'id_empleado' => 'TEST-001',
                    'nombre' => 'Juan Pérez',
                    'cargo' => 'Conductor',
                    'correo' => 'juan.perez@test.com',
                    'telefono' => '555-0123'
                ];
                
            case 'vehiculo':
                return [
                    'id_vehiculo' => 'TEST-V001',
                    'placa' => 'ABC-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'anio' => 2023,
                    'tipo_id' => 'sedan',
                    'estado' => 'disponible'
                ];
                
            case 'sucursal':
                return [
                    'id_sucursal' => 'TEST-S001',
                    'nombre' => 'Sucursal Central Test',
                    'direccion' => 'Av. Principal 123',
                    'ciudad' => 'Lima',
                    'telefono' => '555-0456'
                ];
                
            case 'usuario':
                return [
                    'id' => 'TEST-U001',
                    'name' => 'Usuario Test',
                    'email' => 'usuario.test@example.com',
                    'rol' => 'administrador'
                ];
                
            default:
                return [
                    'id' => 'TEST-001',
                    'test_field' => 'test_value',
                    'timestamp' => now()->toISOString()
                ];
        }
    }
}
