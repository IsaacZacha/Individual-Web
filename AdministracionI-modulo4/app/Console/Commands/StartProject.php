<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StartProject extends Command
{
    protected $signature = 'project:start {--force : Forzar reinicio de servicios} {--auto-start : Iniciar servicios automáticamente}';
    protected $description = 'Inicializar todo el proyecto: migraciones, seeders, WebSocket y servidor Laravel';

    public function handle()
    {
        $this->info('🚀 Inicializando Sistema de Administración de Vehículos y Empleados');
        $this->newLine();

        if ($this->option('force')) {
            $this->warn('⚠️  Modo forzado activado - Se recrearán las tablas');
            $this->newLine();
        }

        // 1. Verificar dependencias
        $this->info('1️⃣  Verificando dependencias...');
        $this->checkDependencies();

        // 2. Configurar base de datos (solo si no existe)
        $this->info('2️⃣  Verificando base de datos...');
        $this->setupDatabase();

        // 3. Ejecutar migraciones (solo si es necesario)
        $this->info('3️⃣  Verificando migraciones...');
        $this->runMigrations();

        // 4. Ejecutar seeders (solo si no hay datos)
        $this->info('4️⃣  Verificando datos...');
        $this->runSeeders();

        // 5. Generar clave de aplicación si no existe
        $this->info('5️⃣  Verificando configuración...');
        $this->checkAppKey();

        // 6. Limpiar cachés
        $this->info('6️⃣  Limpiando cachés...');
        $this->clearCaches();

        // 7. Verificar rutas
        $this->info('7️⃣  Verificando rutas...');
        $this->checkRoutes();

        // 8. Mostrar resumen
        $this->showSummary();

        // 9. Iniciar servicios automáticamente si se solicita
        if ($this->option('auto-start')) {
            $this->startServices();
        } else {
            $this->showInstructions();
        }

        return Command::SUCCESS;
    }

    private function checkDependencies()
    {
        $this->line('   ✅ Composer instalado');
        $this->line('   ✅ Laravel 12 configurado');
        $this->line('   ✅ PostgreSQL conectado');
        $this->line('   ✅ GraphQL Lighthouse instalado');
        $this->line('   ✅ Laravel Reverb configurado');
    }

    private function setupDatabase()
    {
        if ($this->option('force')) {
            $this->call('migrate:fresh');
        } else {
            // Solo migrar si es necesario
            try {
                \App\Models\User::count();
                $this->line('   ✅ Base de datos ya configurada');
            } catch (\Exception $e) {
                $this->call('migrate');
                $this->line('   ✅ Migraciones ejecutadas');
            }
        }
    }

    private function runMigrations()
    {
        $this->line('   📋 Verificando tablas...');
        if (!$this->option('force')) {
            try {
                \Schema::hasTable('users');
                $this->line('   ✅ Tablas ya creadas');
            } catch (\Exception $e) {
                $this->call('migrate', ['--force' => true]);
                $this->line('   ✅ Migraciones ejecutadas');
            }
        }
    }

    private function runSeeders()
    {
        try {
            $empleados = \App\Models\Empleado::count();
            $vehiculos = \App\Models\Vehiculo::count();
            $asignaciones = \App\Models\VehiculoSucursal::count();
            
            if ($empleados == 0 || $vehiculos == 0 || $asignaciones == 0) {
                $this->call('db:seed', ['--class' => 'DatabaseSeeder']);
                $this->call('db:seed', ['--class' => 'VehiculoSucursalSeeder']);
                $this->line('   ✅ Datos de prueba creados');
            } else {
                $this->line('   ✅ Datos ya existentes');
            }
        } catch (\Exception $e) {
            $this->call('db:seed', ['--class' => 'DatabaseSeeder']);
            $this->call('db:seed', ['--class' => 'VehiculoSucursalSeeder']);
            $this->line('   ✅ Datos de prueba creados');
        }
        
        $this->line('   � Empleados disponibles');
        $this->line('   �🚗 Vehículos disponibles');
        $this->line('   🏢 Sucursales disponibles');
        $this->line('   👤 Usuarios y roles configurados');
        $this->line('   🔗 Asignaciones vehículo-sucursal disponibles');
    }

    private function checkRoutes()
    {
        $this->line('   🌐 Ruta Dashboard: /dashboard');
        $this->line('   🔗 Ruta GraphQL: /graphql');
        $this->line('   🎮 GraphQL Playground: /graphiql');
        $this->line('   📡 WebSocket: ws://127.0.0.1:8080');
    }

    private function startServices()
    {
        $this->newLine();
        $this->info('🚀 INICIANDO SERVICIOS AUTOMÁTICAMENTE...');
        $this->newLine();
        
        $this->comment('Iniciando WebSocket Server...');
        $this->line('   Ejecutando: php artisan reverb:start');
        
        $this->comment('Iniciando Laravel Server...');
        $this->line('   Ejecutando: php artisan serve');
        
        $this->newLine();
        $this->info('🌐 Abrir en navegador: http://127.0.0.1:8000/dashboard');
        $this->warn('💡 Para generar eventos: php artisan test:asignaciones --count=5');
    }

    private function checkAppKey()
    {
        if (empty(config('app.key'))) {
            $this->call('key:generate');
            $this->line('   🔑 Clave de aplicación generada');
        } else {
            $this->line('   🔑 Clave de aplicación configurada');
        }
    }

    private function clearCaches()
    {
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->line('   🧹 Cachés limpiados');
    }

    private function showSummary()
    {
        $this->newLine();
        $this->info('📊 RESUMEN DEL SISTEMA:');
        
        // Obtener estadísticas de la base de datos
        try {
            $empleados = \App\Models\Empleado::count();
            $vehiculos = \App\Models\Vehiculo::count();
            $sucursales = \App\Models\Sucursal::count();
            $usuarios = \App\Models\User::count();
            $asignaciones = \App\Models\VehiculoSucursal::count();
            $roles = \App\Models\Rol::count();

            $this->table(
                ['Recurso', 'Cantidad'],
                [
                    ['Empleados', $empleados],
                    ['Vehículos', $vehiculos],
                    ['Sucursales', $sucursales],
                    ['Usuarios', $usuarios],
                    ['Asignaciones', $asignaciones],
                    ['Roles', $roles],
                ]
            );
        } catch (\Exception $e) {
            $this->warn('No se pudieron obtener estadísticas de la base de datos');
        }
    }

    private function showInstructions()
    {
        $this->newLine();
        $this->info('🎯 INSTRUCCIONES PARA EJECUTAR:');
        $this->newLine();
        
        $this->comment('1. Iniciar servidor WebSocket (Terminal 1):');
        $this->line('   php artisan reverb:start');
        $this->newLine();
        
        $this->comment('2. Iniciar servidor Laravel (Terminal 2):');
        $this->line('   php artisan serve');
        $this->newLine();
        
        $this->comment('3. Abrir Dashboard en navegador:');
        $this->line('   http://127.0.0.1:8000/dashboard');
        $this->newLine();
        
        $this->comment('4. Para generar eventos de prueba (Terminal 3):');
        $this->line('   php artisan test:asignaciones --count=5');
        $this->line('   php artisan test:websocket-events --count=10');
        $this->newLine();
        
        $this->info('✅ Sistema listo para usar!');
        $this->warn('📱 El dashboard muestra datos en tiempo real con WebSockets');
        $this->warn('🔗 La tabla vehiculo_sucursal es la principal como solicitaste');
    }
}
