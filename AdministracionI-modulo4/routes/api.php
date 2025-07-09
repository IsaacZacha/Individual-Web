<?php
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VehiculoSucursalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\SystemTestController;
use App\Services\NotificationService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// System Testing Routes
Route::prefix('test')->group(function () {
    Route::post('create-empleado-with-event', [SystemTestController::class, 'createEmpleadoWithEvent']);
    Route::post('create-vehiculo-assign-with-events', [SystemTestController::class, 'createVehiculoAssignWithEvents']);
    Route::post('run-artisan', [SystemTestController::class, 'runArtisanCommand']);
});

// Dashboard API Routes
Route::prefix('dashboard')->group(function () {
    Route::get('data', [SystemTestController::class, 'getDashboardData']);
});

// System Info
Route::get('system/info', [SystemTestController::class, 'getSystemInfo']);

// ===== RUTAS EXISTENTES =====

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('sucursales', SucursalController::class);
    Route::apiResource('vehiculos', VehiculoController::class);
    Route::apiResource('vehiculo-sucursal', VehiculoSucursalController::class);
    Route::apiResource('users', UserController::class);
    
    // Rutas específicas para roles con parámetro personalizado
    Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    Route::post('roles', [RolController::class, 'store'])->name('roles.store');
    Route::get('roles/{id_rol}', [RolController::class, 'show'])->name('roles.show');
    Route::put('roles/{id_rol}', [RolController::class, 'update'])->name('roles.update');
    Route::delete('roles/{id_rol}', [RolController::class, 'destroy'])->name('roles.destroy');
    
    Route::apiResource('empleados', EmpleadoController::class);
    Route::post('/gemini/preguntar', [GeminiController::class, 'preguntar']);


});

// Registro y login públicos
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Health check endpoint
Route::get('/health', function () {
    $notificationService = app(NotificationService::class);
    
    return response()->json([
        'status' => 'ok',
        'service' => 'Laravel API',
        'timestamp' => now()->toISOString(),
        'database' => 'connected',
        'websocket_service' => $notificationService->isWebSocketServiceAvailable() ? 'available' : 'unavailable',
        'version' => app()->version()
    ]);
});
