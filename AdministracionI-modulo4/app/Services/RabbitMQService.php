<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use Illuminate\Support\Facades\Log;

/**
 * Servicio RabbitMQ para CloudAMQP
 * URL: amqps://swozzeyr:Sb3-3vjnEU-w9Q4eZkJIrqPo6XcKbiDF@shark.rmq.cloudamqp.com/swozzeyr
 */
class RabbitMQService
{
    private $connection;
    private $channel;
    
    // Configuración desde la URL de CloudAMQP
    private $host = 'shark.rmq.cloudamqp.com';
    private $port = 5672;
    private $sslPort = 5671;
    private $user = 'swozzeyr';
    private $password = 'Sb3-3vjnEU-w9Q4eZkJIrqPo6XcKbiDF';
    private $vhost = 'swozzeyr';
    private $useSSL = true;

    /**
     * Exchanges para diferentes tipos de eventos
     */
    const EXCHANGES = [
        'empleados' => 'microservice.empleados',
        'vehiculos' => 'microservice.vehiculos',
        'sucursales' => 'microservice.sucursales',
        'usuarios' => 'microservice.usuarios',
        'asignaciones' => 'microservice.asignaciones',
        'notificaciones' => 'microservice.notificaciones'
    ];

    /**
     * Colas para cada microservicio
     */
    const QUEUES = [
        'empleados' => 'queue.empleados',
        'vehiculos' => 'queue.vehiculos',
        'sucursales' => 'queue.sucursales',
        'usuarios' => 'queue.usuarios',
        'asignaciones' => 'queue.asignaciones',
        'notificaciones' => 'queue.notificaciones'
    ];

    public function __construct()
    {
        $this->connect();
    }

    /**
     * Establecer conexión con CloudAMQP
     */
    private function connect(): void
    {
        try {
            if ($this->useSSL) {
                // Conexión SSL para producción (CloudAMQP)
                $this->connection = new AMQPSSLConnection(
                    $this->host,
                    $this->sslPort,
                    $this->user,
                    $this->password,
                    $this->vhost,
                    [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                );
            } else {
                // Conexión normal
                $this->connection = new AMQPStreamConnection(
                    $this->host,
                    $this->port,
                    $this->user,
                    $this->password,
                    $this->vhost
                );
            }

            $this->channel = $this->connection->channel();
            $this->setupExchangesAndQueues();

            Log::info("🐰 Conectado exitosamente a CloudAMQP", [
                'host' => $this->host,
                'vhost' => $this->vhost,
                'ssl' => $this->useSSL
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error conectando a RabbitMQ", [
                'error' => $e->getMessage(),
                'host' => $this->host
            ]);
            throw $e;
        }
    }

    /**
     * Configurar exchanges y colas
     */
    private function setupExchangesAndQueues(): void
    {
        foreach (self::EXCHANGES as $key => $exchange) {
            // Declarar exchange
            $this->channel->exchange_declare(
                $exchange,
                'topic',
                false, // passive
                true,  // durable
                false  // auto_delete
            );

            // Declarar cola
            $queue = self::QUEUES[$key];
            $this->channel->queue_declare(
                $queue,
                false, // passive
                true,  // durable
                false, // exclusive
                false  // auto_delete
            );

            // Bind cola a exchange
            $this->channel->queue_bind($queue, $exchange, "{$key}.*");
        }

        Log::info("🔧 Exchanges y colas configurados correctamente");
    }

    /**
     * Publicar evento de empleado
     */
    public function publishEmpleadoEvent(array $empleadoData, string $action = 'created'): void
    {
        $this->publishEvent('empleados', $empleadoData, $action);
    }

    /**
     * Publicar evento de vehículo
     */
    public function publishVehiculoEvent(array $vehiculoData, string $action = 'created'): void
    {
        $this->publishEvent('vehiculos', $vehiculoData, $action);
    }

    /**
     * Publicar evento de sucursal
     */
    public function publishSucursalEvent(array $sucursalData, string $action = 'created'): void
    {
        $this->publishEvent('sucursales', $sucursalData, $action);
    }

    /**
     * Publicar evento de usuario
     */
    public function publishUsuarioEvent(array $usuarioData, string $action = 'created'): void
    {
        $this->publishEvent('usuarios', $usuarioData, $action);
    }

    /**
     * Publicar evento de asignación
     */
    public function publishAsignacionEvent(array $asignacionData, string $action = 'assigned'): void
    {
        $this->publishEvent('asignaciones', $asignacionData, $action);
    }

    /**
     * Método principal para publicar eventos
     */
    private function publishEvent(string $entityType, array $data, string $action): void
    {
        try {
            $exchange = self::EXCHANGES[$entityType];
            $routingKey = "{$entityType}.{$action}";

            $payload = [
                'timestamp' => now()->toISOString(),
                'entity_type' => $entityType,
                'action' => $action,
                'data' => $data,
                'microservice' => 'administracion-modulo4',
                'version' => '1.0',
                'correlation_id' => uniqid('msg_', true)
            ];

            $message = new AMQPMessage(
                json_encode($payload),
                [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    'content_type' => 'application/json',
                    'timestamp' => time(),
                    'correlation_id' => $payload['correlation_id']
                ]
            );

            $this->channel->basic_publish($message, $exchange, $routingKey);

            Log::info("📤 Evento publicado en RabbitMQ", [
                'exchange' => $exchange,
                'routing_key' => $routingKey,
                'entity_type' => $entityType,
                'action' => $action,
                'correlation_id' => $payload['correlation_id']
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error publicando evento en RabbitMQ", [
                'entity_type' => $entityType,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Consumir mensajes de una cola específica
     */
    public function consumeQueue(string $entityType, callable $callback): void
    {
        $queue = self::QUEUES[$entityType];

        Log::info("👂 Iniciando consumo de cola", ['queue' => $queue]);

        $this->channel->basic_consume(
            $queue,
            '',
            false, // no_local
            false, // no_ack
            false, // exclusive
            false, // nowait
            function (AMQPMessage $msg) use ($callback) {
                try {
                    $data = json_decode($msg->body, true);
                    
                    Log::info("📨 Mensaje recibido", [
                        'correlation_id' => $data['correlation_id'] ?? 'unknown',
                        'entity_type' => $data['entity_type'] ?? 'unknown',
                        'action' => $data['action'] ?? 'unknown'
                    ]);

                    // Ejecutar callback
                    $callback($data);

                    // Acknowledge del mensaje usando el canal
                    $this->channel->basic_ack($msg->delivery_info['delivery_tag']);

                } catch (\Exception $e) {
                    Log::error("❌ Error procesando mensaje", [
                        'error' => $e->getMessage(),
                        'message_body' => $msg->body
                    ]);

                    // Reject y requeue el mensaje
                    $this->channel->basic_nack($msg->delivery_info['delivery_tag'], false, true);
                }
            }
        );

        // Mantener la conexión activa para consumir mensajes
        $timeout = 30; // 30 segundos de timeout
        $startTime = time();
        
        while (count($this->channel->callbacks) && (time() - $startTime) < $timeout) {
            $this->channel->wait(null, false, 1);
        }
    }

    /**
     * Obtener estadísticas de las colas
     */
    public function getQueueStats(): array
    {
        $stats = [];

        foreach (self::QUEUES as $entityType => $queueName) {
            try {
                [$messageCount, $consumerCount] = $this->channel->queue_declare($queueName, true);
                
                $stats[$entityType] = [
                    'queue_name' => $queueName,
                    'message_count' => $messageCount,
                    'consumer_count' => $consumerCount,
                    'exchange' => self::EXCHANGES[$entityType]
                ];
            } catch (\Exception $e) {
                $stats[$entityType] = [
                    'queue_name' => $queueName,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $stats;
    }

    /**
     * Verificar conexión
     */
    public function healthCheck(): array
    {
        try {
            $isConnected = $this->connection && $this->connection->isConnected();
            $channelOpen = $this->channel && isset($this->channel);

            return [
                'status' => $isConnected && $channelOpen ? 'healthy' : 'unhealthy',
                'connection' => $isConnected,
                'channel' => $channelOpen,
                'host' => $this->host,
                'vhost' => $this->vhost,
                'ssl' => $this->useSSL,
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Cerrar conexión
     */
    public function __destruct()
    {
        try {
            if ($this->channel) {
                $this->channel->close();
            }
            if ($this->connection) {
                $this->connection->close();
            }
        } catch (\Exception $e) {
            Log::warning("Warning cerrando conexión RabbitMQ", ['error' => $e->getMessage()]);
        }
    }
}
