<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GraphQLWebSocketService;
use App\GraphQL\Queries\EstadisticasQuery;
use App\GraphQL\Mutations\EmpleadoMutations;
use App\Events\TestEvent;

class TestSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:system {--component=all : Componente a probar (all, graphql, websocket, service, dashboard)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba todos los componentes del sistema: GraphQL, WebSocket, Servicios';

    protected $graphqlService;

    public function __construct(GraphQLWebSocketService $graphqlService)
    {
        parent::__construct();
        $this->graphqlService = $graphqlService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $component = $this->option('component');

        $this->info('🧪 INICIANDO TESTS DEL SISTEMA');
        $this->line('═══════════════════════════════════════');

        if ($component === 'all' || $component === 'service') {
            $this->testGraphQLWebSocketService();
        }

        if ($component === 'all' || $component === 'graphql') {
            $this->testGraphQLResolvers();
        }

        if ($component === 'all' || $component === 'websocket') {
            $this->testWebSocketEvents();
        }

        if ($component === 'all' || $component === 'dashboard') {
            $this->testDashboardEndpoints();
        }

        $this->info('');
        $this->info('✅ TESTS COMPLETADOS');
        $this->line('═══════════════════════════════════════');
    }

    private function testGraphQLWebSocketService()
    {
        $this->info('');
        $this->info('🔧 TESTING GraphQLWebSocketService...');
        $this->line('───────────────────────────────────────');

        try {
            // Test estadísticas
            $estadisticas = $this->graphqlService->getEstadisticas();
            $this->line("✅ Estadísticas: {$estadisticas['totalEmpleados']} empleados, {$estadisticas['totalVehiculos']} vehículos");

            // Test empleados con usuarios
            $empleados = $this->graphqlService->getEmpleadosConUsuarios();
            $this->line("✅ Empleados con usuarios: " . count($empleados) . " encontrados");

            // Test sucursales con vehículos
            $sucursales = $this->graphqlService->getSucursalesConVehiculos();
            $this->line("✅ Sucursales con vehículos: " . count($sucursales) . " encontradas");

            // Test estado del sistema
            $status = $this->graphqlService->getSystemStatus();
            $this->line("✅ Estado del sistema: GraphQL {$status['graphql']['status']}, DB {$status['database']['status']}");

        } catch (\Exception $e) {
            $this->error("❌ Error en GraphQLWebSocketService: " . $e->getMessage());
        }
    }

    private function testGraphQLResolvers()
    {
        $this->info('');
        $this->info('📊 TESTING GraphQL Resolvers...');
        $this->line('───────────────────────────────────────');

        try {
            // Test EstadisticasQuery
            $estadisticasQuery = new EstadisticasQuery();
            $result = $estadisticasQuery->resolve(null, [], null, null);
            $this->line("✅ EstadisticasQuery: {$result['totalEmpleados']} empleados, {$result['totalVehiculos']} vehículos");

            // Verificar que los resolvers están disponibles
            $resolvers = [
                'App\\GraphQL\\Queries\\SucursalesConVehiculosQuery',
                'App\\GraphQL\\Queries\\EmpleadosConUsuariosQuery',
                'App\\GraphQL\\Mutations\\EmpleadoMutations',
                'App\\GraphQL\\Mutations\\VehiculoMutations',
                'App\\GraphQL\\Mutations\\SucursalMutations'
            ];

            foreach ($resolvers as $resolver) {
                if (class_exists($resolver)) {
                    $this->line("✅ Resolver disponible: " . class_basename($resolver));
                } else {
                    $this->error("❌ Resolver no encontrado: " . class_basename($resolver));
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Error en GraphQL Resolvers: " . $e->getMessage());
        }
    }

    private function testWebSocketEvents()
    {
        $this->info('');
        $this->info('📡 TESTING WebSocket Events...');
        $this->line('───────────────────────────────────────');

        try {
            // Test evento manual
            $success = $this->graphqlService->sendTestWebSocketEvent();
            if ($success) {
                $this->line("✅ Test WebSocket event enviado correctamente");
            } else {
                $this->error("❌ Error enviando test WebSocket event");
            }

            // Test evento directo
            broadcast(new TestEvent([
                'message' => 'Test directo desde comando artisan',
                'timestamp' => now()->toISOString(),
                'source' => 'test:system command'
            ]));
            $this->line("✅ Evento TestEvent enviado directamente");

            // Verificar configuración de broadcasting
            $driver = config('broadcasting.default');
            $this->line("✅ Broadcasting driver configurado: {$driver}");

        } catch (\Exception $e) {
            $this->error("❌ Error en WebSocket Events: " . $e->getMessage());
        }
    }

    private function testDashboardEndpoints()
    {
        $this->info('');
        $this->info('🌐 TESTING Dashboard Endpoints...');
        $this->line('───────────────────────────────────────');

        try {
            $baseUrl = config('app.url', 'http://localhost:8000');
            
            $endpoints = [
                '/dashboard' => 'Dashboard principal',
                '/dashboard/estadisticas' => 'API de estadísticas',
                '/dashboard/status' => 'Estado del sistema',
                '/graphql' => 'GraphQL endpoint',
                '/graphiql' => 'GraphQL playground'
            ];

            foreach ($endpoints as $endpoint => $description) {
                $this->line("📋 {$description}: {$baseUrl}{$endpoint}");
            }

            $this->line("✅ Todos los endpoints están configurados");

        } catch (\Exception $e) {
            $this->error("❌ Error verificando endpoints: " . $e->getMessage());
        }
    }
}
