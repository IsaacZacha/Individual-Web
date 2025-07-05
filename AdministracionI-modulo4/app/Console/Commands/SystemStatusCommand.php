<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Empleado;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\User;

class SystemStatusCommand extends Command
{
    protected $signature = 'system:status {--json : Output as JSON}';
    protected $description = 'Verificar el estado del sistema de microservicios';

    public function handle()
    {
        $this->info('🔍 Verificando estado del sistema...');
        $this->newLine();

        $status = [
            'database' => $this->checkDatabase(),
            'graphql' => $this->checkGraphQL(),
            'broadcasting' => $this->checkBroadcasting(),
            'reverb' => $this->checkReverb(),
            'data' => $this->checkData(),
            'files' => $this->checkFiles()
        ];

        if ($this->option('json')) {
            $this->line(json_encode($status, JSON_PRETTY_PRINT));
            return;
        }

        $this->displayStatus($status);
        $this->displaySummary($status);
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            $connection = config('database.default');
            
            return [
                'status' => 'online',
                'connection' => $connection,
                'database' => config("database.connections.{$connection}.database")
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkGraphQL()
    {
        try {
            $schemaPath = base_path('graphql/schema.graphql');
            $configPath = config_path('lighthouse.php');
            
            return [
                'status' => file_exists($schemaPath) && file_exists($configPath) ? 'configured' : 'missing_files',
                'schema_exists' => file_exists($schemaPath),
                'config_exists' => file_exists($configPath),
                'endpoint' => config('lighthouse.route.uri', '/graphql'),
                'schema_path' => $schemaPath
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkBroadcasting()
    {
        try {
            $default = config('broadcasting.default');
            $config = config("broadcasting.connections.{$default}");
            
            return [
                'status' => $default !== 'null' ? 'configured' : 'disabled',
                'driver' => $default,
                'config' => $config ? 'found' : 'missing'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkReverb()
    {
        try {
            $host = config('broadcasting.connections.reverb.host', 'localhost');
            $port = config('broadcasting.connections.reverb.port', 8080);
            
            $connection = @fsockopen($host, $port, $errno, $errstr, 1);
            
            if ($connection) {
                fclose($connection);
                $status = 'online';
            } else {
                $status = 'offline';
            }
            
            return [
                'status' => $status,
                'host' => $host,
                'port' => $port,
                'app_key' => config('broadcasting.connections.reverb.key')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkData()
    {
        try {
            return [
                'status' => 'available',
                'empleados' => Empleado::count(),
                'vehiculos' => Vehiculo::count(),
                'sucursales' => Sucursal::count(),
                'usuarios' => User::count()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkFiles()
    {
        $files = [
            'dashboard_view' => resource_path('views/dashboard.blade.php'),
            'graphql_schema' => base_path('graphql/schema.graphql'),
            'lighthouse_config' => config_path('lighthouse.php'),
            'broadcasting_config' => config_path('broadcasting.php'),
            'start_script' => base_path('start-system.bat')
        ];

        $result = ['status' => 'complete', 'files' => []];
        
        foreach ($files as $name => $path) {
            $exists = file_exists($path);
            $result['files'][$name] = [
                'exists' => $exists,
                'path' => $path
            ];
            
            if (!$exists && $result['status'] === 'complete') {
                $result['status'] = 'incomplete';
            }
        }

        return $result;
    }

    private function displayStatus($status)
    {
        $this->info('📊 ESTADO DEL SISTEMA');
        $this->line('═══════════════════════════════════════');

        // Database
        $this->displayComponent('🗄️  Base de Datos', $status['database']);
        
        // GraphQL
        $this->displayComponent('🚀 GraphQL Gateway', $status['graphql']);
        
        // Broadcasting
        $this->displayComponent('📡 Broadcasting', $status['broadcasting']);
        
        // Reverb
        $this->displayComponent('🔌 WebSocket (Reverb)', $status['reverb']);
        
        // Data
        $this->displayComponent('📈 Datos', $status['data']);
        
        // Files
        $this->displayComponent('📁 Archivos', $status['files']);
    }

    private function displayComponent($name, $data)
    {
        $statusEmoji = $this->getStatusEmoji($data['status']);
        $this->line("$name: $statusEmoji {$data['status']}");
        
        if (isset($data['error'])) {
            $this->line("   ❌ Error: {$data['error']}");
        } else {
            foreach ($data as $key => $value) {
                if ($key === 'status') continue;
                if (is_array($value)) continue;
                if (is_bool($value)) $value = $value ? 'Sí' : 'No';
                $this->line("   📋 $key: $value");
            }
        }
        $this->newLine();
    }

    private function getStatusEmoji($status)
    {
        return match($status) {
            'online', 'configured', 'available', 'complete' => '🟢',
            'offline', 'error', 'missing_files', 'incomplete' => '🔴',
            'disabled' => '🟡',
            default => '⚪'
        };
    }

    private function displaySummary($status)
    {
        $this->info('📋 RESUMEN DE SERVICIOS');
        $this->line('═══════════════════════════════════════');
        
        $allGreen = true;
        foreach ($status as $component => $data) {
            $isHealthy = in_array($data['status'], ['online', 'configured', 'available', 'complete']);
            if (!$isHealthy) $allGreen = false;
        }

        if ($allGreen) {
            $this->info('✅ Todos los servicios están funcionando correctamente');
            $this->newLine();
            $this->info('🌐 URLs disponibles:');
            $this->line('   • Dashboard: http://localhost:8000/dashboard');
            $this->line('   • GraphQL: http://localhost:8000/graphql');
            $this->line('   • GraphiQL: http://localhost:8000/graphiql');
            $this->line('   • API Status: http://localhost:8000/dashboard/status');
        } else {
            $this->warn('⚠️  Algunos servicios necesitan atención');
            $this->newLine();
            $this->info('🔧 Para iniciar el sistema completo:');
            $this->line('   start-system.bat');
        }
        
        $this->newLine();
        $this->info('📖 Para más detalles: php artisan system:status --json');
    }
}
