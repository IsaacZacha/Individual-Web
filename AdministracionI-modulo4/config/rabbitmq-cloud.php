<?php

/**
 * CONFIGURACIÓN PARA RABBITMQ CLOUD
 * 
 * Cuando tengas la URL de RabbitMQ Cloud, sigue estos pasos:
 * 
 * 1. Si tu URL es tipo CloudAMQP: amqps://user:pass@host.cloudamqp.com/vhost
 * 2. Si tu URL es tipo Amazon MQ: amqps://user:pass@mq.region.amazonaws.com:5671/vhost
 * 3. Si tu URL es tipo Azure Service Bus: usar conexión AMQP
 * 
 */

return [
    
    // CLOUDAMQP.COM (Popular para microservicios)
    'cloudamqp' => [
        'example_url' => 'amqps://username:password@beetle.rmq.cloudamqp.com/vhost-name',
        'env_config' => [
            'RABBITMQ_HOST' => 'beetle.rmq.cloudamqp.com',
            'RABBITMQ_PORT' => '5672',
            'RABBITMQ_VHOST' => 'vhost-name',
            'RABBITMQ_LOGIN' => 'username',
            'RABBITMQ_PASSWORD' => 'password',
            'RABBITMQ_SSL_ENABLED' => 'true',
            'RABBITMQ_SSL_PORT' => '5671'
        ]
    ],

    // AMAZON MQ
    'amazon_mq' => [
        'example_url' => 'amqps://username:password@mq.us-east-1.amazonaws.com:5671/',
        'env_config' => [
            'RABBITMQ_HOST' => 'mq.us-east-1.amazonaws.com',
            'RABBITMQ_PORT' => '5671',
            'RABBITMQ_VHOST' => '/',
            'RABBITMQ_LOGIN' => 'username',
            'RABBITMQ_PASSWORD' => 'password',
            'RABBITMQ_SSL_ENABLED' => 'true'
        ]
    ],

    // AZURE SERVICE BUS
    'azure_servicebus' => [
        'example_url' => 'amqps://namespace.servicebus.windows.net',
        'env_config' => [
            'RABBITMQ_HOST' => 'namespace.servicebus.windows.net',
            'RABBITMQ_PORT' => '5671',
            'RABBITMQ_VHOST' => '/',
            'RABBITMQ_LOGIN' => 'access-key-name',
            'RABBITMQ_PASSWORD' => 'access-key',
            'RABBITMQ_SSL_ENABLED' => 'true'
        ]
    ],

    // UPSTASH (Redis-like para RabbitMQ)
    'upstash' => [
        'example_url' => 'amqps://username:password@host.upstash.io:5671',
        'env_config' => [
            'RABBITMQ_HOST' => 'host.upstash.io',
            'RABBITMQ_PORT' => '5671',
            'RABBITMQ_VHOST' => '/',
            'RABBITMQ_LOGIN' => 'username',
            'RABBITMQ_PASSWORD' => 'password',
            'RABBITMQ_SSL_ENABLED' => 'true'
        ]
    ],

    // CUSTOM CONFIGURATION
    'instructions' => [
        'step_1' => 'Obtén tu URL de RabbitMQ Cloud del proveedor',
        'step_2' => 'Parsea la URL para extraer: host, port, user, password, vhost',
        'step_3' => 'Actualiza las variables en .env usando este archivo como guía',
        'step_4' => 'Cambia QUEUE_CONNECTION=redis a QUEUE_CONNECTION=rabbitmq en .env',
        'step_5' => 'Instala: composer require vladimir-yuldashev/laravel-queue-rabbitmq',
        'step_6' => 'Ejecuta: php artisan queue:work rabbitmq'
    ]
];
