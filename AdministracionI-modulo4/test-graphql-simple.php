<?php

/**
 * Test GraphQL Simple
 * Ejecutar: php test-graphql-simple.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Crear la aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Crear una petición GraphQL de prueba
$request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
    'query' => '{ empleados { id_empleado nombre correo } }'
]));

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

try {
    // Procesar la petición
    $response = $kernel->handle($request);
    
    echo "✅ Estado HTTP: " . $response->getStatusCode() . "\n";
    echo "📊 Respuesta GraphQL:\n";
    echo $response->getContent() . "\n";
    
    // Verificar si la respuesta es JSON válida
    $data = json_decode($response->getContent(), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Respuesta JSON válida\n";
        
        if (isset($data['errors'])) {
            echo "🚨 Errores GraphQL encontrados:\n";
            foreach ($data['errors'] as $error) {
                echo "  - " . $error['message'] . "\n";
            }
        } else {
            echo "✅ Query GraphQL ejecutada sin errores\n";
        }
    } else {
        echo "🚨 Error: Respuesta no es JSON válida\n";
    }
    
} catch (Exception $e) {
    echo "🚨 Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    $kernel->terminate($request, $response ?? null);
}
