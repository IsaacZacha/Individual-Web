<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\VehiculoSucursal;
use App\Models\Empleado;
use App\Events\EmpleadoCreado;
use App\Events\VehiculoCreado;
use App\Events\SucursalCreada;
use App\Events\VehiculoSucursalAsignado;
use App\Events\VehiculoSucursalDesasignado;
use App\Events\VehiculoSucursalActualizado;

class TestWebSocketEvents extends Command
{
    protected $signature = 'test:websocket-events {--count=5 : Número de eventos a generar}';
    protected $description = 'Generar eventos de prueba para testing WebSocket en tiempo real';

    public function handle()
    {
        $count = $this->option('count');
        
        $this->info("🚀 Generando {$count} eventos de prueba...");

        for ($i = 1; $i <= $count; $i++) {
            $this->info("📡 Evento {$i}/{$count}");

            $eventType = rand(1, 4);
            
            switch ($eventType) {
                case 1:
                    $this->testEmpleadoEvent();
                    break;
                case 2:
                    $this->testVehiculoEvent();
                    break;
                case 3:
                    $this->testSucursalEvent();
                    break;
                case 4:
                    $this->testVehiculoSucursalEvent();
                    break;
            }

            // Esperar 2 segundos entre eventos
            sleep(2);
        }

        $this->info("✅ Eventos generados correctamente");
    }

    private function testEmpleadoEvent()
    {
        $empleado = Empleado::inRandomOrder()->first();
        if ($empleado) {
            event(new EmpleadoCreado($empleado));
            $this->line("👤 Evento Empleado: {$empleado->nombre}");
        }
    }

    private function testVehiculoEvent()
    {
        $vehiculo = Vehiculo::inRandomOrder()->first();
        if ($vehiculo) {
            event(new VehiculoCreado($vehiculo));
            $this->line("🚗 Evento Vehículo: {$vehiculo->placa} - {$vehiculo->marca} {$vehiculo->modelo}");
        }
    }

    private function testSucursalEvent()
    {
        $sucursal = Sucursal::inRandomOrder()->first();
        if ($sucursal) {
            event(new SucursalCreada($sucursal));
            $this->line("🏢 Evento Sucursal: {$sucursal->nombre} - {$sucursal->ciudad}");
        }
    }

    private function testVehiculoSucursalEvent()
    {
        // Intentar crear una nueva asignación
        $vehiculo = Vehiculo::inRandomOrder()->first();
        $sucursal = Sucursal::inRandomOrder()->first();

        if ($vehiculo && $sucursal) {
            // Verificar si ya existe la asignación
            $asignacion = VehiculoSucursal::where('id_vehiculo', $vehiculo->id_vehiculo)
                                          ->where('id_sucursal', $sucursal->id_sucursal)
                                          ->first();

            if (!$asignacion) {
                // Crear nueva asignación
                $asignacion = VehiculoSucursal::create([
                    'id_vehiculo' => $vehiculo->id_vehiculo,
                    'id_sucursal' => $sucursal->id_sucursal,
                    'fecha_asignacion' => now(),
                ]);

                event(new VehiculoSucursalAsignado($asignacion->load(['vehiculo', 'sucursal'])));
                $this->line("🔗 Evento Asignación: {$vehiculo->placa} ↔ {$sucursal->nombre}");
            } else {
                // Actualizar asignación existente
                $asignacion->fecha_asignacion = now();
                $asignacion->save();

                event(new VehiculoSucursalActualizado($asignacion->load(['vehiculo', 'sucursal'])));
                $this->line("🔄 Evento Actualización: {$vehiculo->placa} ↔ {$sucursal->nombre}");
            }
        }
    }
}
