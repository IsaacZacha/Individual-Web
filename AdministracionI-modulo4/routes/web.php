<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebSocketTestController;
use App\Http\Controllers\SimpleTestController;

Route::get('/', function () {
    return view('welcome');
});

// GraphQL Playground - Evita errores al navegar directamente a /graphql

Route::get('/graphql', function () {
    return response()->json([
        'message' => 'GraphQL endpoint está funcionando. Use POST con query parameter.',
        'playground' => 'Use GraphQL Playground o Postman para enviar queries.',
        'example' => [
            'method' => 'POST',
            'url' => url('/graphql'),
            'headers' => ['Content-Type' => 'application/json'],
            'body' => [
                'query' => '{ empleados { id_empleado nombre } }'
            ]
        ]
    ]);
});

// Dashboard routes
Route::get('/dashboard', function () {
    return redirect('/dashboard-v2');
})->name('dashboard');

Route::get('/dashboard-v2', function () {
    return response()->view('dashboard-v2')->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('dashboard-v2');
Route::get('/test-dashboard', function () {
    return view('test-dashboard');
})->name('test-dashboard');
Route::get('/test-websocket', function () {
    return view('test-websocket');
})->name('test-websocket');
Route::get('/websocket-test', function () {
    return view('websocket-test');
})->name('websocket-test');
Route::get('/dashboard/estadisticas', [DashboardController::class, 'estadisticas'])->name('dashboard.estadisticas');
Route::get('/dashboard/status', [DashboardController::class, 'status'])->name('dashboard.status');
Route::post('/dashboard/test-websocket', [DashboardController::class, 'testWebSocket'])->name('dashboard.test-websocket');

// WebSocket Test Routes - Para probar eventos en tiempo real
Route::prefix('websocket-test')->name('websocket.test.')->group(function () {
    Route::get('/empleado-creado', [WebSocketTestController::class, 'testEmpleadoCreado'])->name('empleado-creado');
    Route::get('/vehiculo-creado', [WebSocketTestController::class, 'testVehiculoCreado'])->name('vehiculo-creado');
    Route::get('/general-event', [WebSocketTestController::class, 'testGeneralEvent'])->name('general-event');
    Route::get('/multiple-events', [WebSocketTestController::class, 'testMultipleEvents'])->name('multiple-events');
    Route::get('/status', [WebSocketTestController::class, 'getWebSocketStatus'])->name('status');
});

// Simple Test Routes - Para pruebas básicas sin broadcasting
Route::prefix('simple-test')->name('simple.test.')->group(function () {
    Route::get('/basic', [SimpleTestController::class, 'testSimple'])->name('basic');
    Route::get('/activity', [SimpleTestController::class, 'testActivity'])->name('activity');
});
