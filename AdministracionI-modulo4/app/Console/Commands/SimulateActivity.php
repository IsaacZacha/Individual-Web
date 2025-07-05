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
use App\Events\VehiculoSucursalActualizado;

class SimulateActivity extends Command
{
    protected $signature = 'simulate:activity {--duration=300 : Duración en segundos}';
    protected $description = 'Simular actividad continua del sistema para demostración';

    public function handle()
    {
        $duration = $this->option('duration');
        $endTime = time() + $duration;
        
        $this->info("🎬 Iniciando simulación de actividad por {$duration} segundos...");
        
        while (time() < $endTime) {
            $this->generateRandomEvent();
            
            // Esperar entre 10 y 30 segundos entre eventos
            $wait = rand(10, 30);
            $this->line("⏰ Esperando {$wait} segundos...");
            sleep($wait);
        }
        
        $this->info("✅ Simulación completada");
    }

    private function generateRandomEvent()
    {
        $eventType = rand(1, 4);
        
        switch ($eventType) {
            case 1:
                $this->simulateVehiculoEvent();
                break;
            case 2:
                $this->simulateEmpleadoEvent();
                break;
            case 3:
                $this->simulateSucursalEvent();
                break;
            case 4:
                $this->simulateAsignacionEvent();
                break;
        }
    }

    private function simulateVehiculoEvent()
    {
        $vehiculo = Vehiculo::inRandomOrder()->first();
        if ($vehiculo) {
            event(new VehiculoCreado($vehiculo));
            $this->line("🚗 Evento Vehículo: {$vehiculo->placa} - {$vehiculo->marca} {$vehiculo->modelo}");
        }
    }

    private function simulateEmpleadoEvent()
    {
        $empleado = Empleado::inRandomOrder()->first();
        if ($empleado) {
            event(new EmpleadoCreado($empleado));
            $this->line("👤 Evento Empleado: {$empleado->nombre}");
        }
    }

    private function simulateSucursalEvent()
    {
        $sucursal = Sucursal::inRandomOrder()->first();
        if ($sucursal) {
            event(new SucursalCreada($sucursal));
            $this->line("🏢 Evento Sucursal: {$sucursal->nombre} - {$sucursal->ciudad}");
        }
    }

    private function simulateAsignacionEvent()
    {
        $vehiculo = Vehiculo::inRandomOrder()->first();
        $sucursal = Sucursal::inRandomOrder()->first();

        if ($vehiculo && $sucursal) {
            $asignacion = VehiculoSucursal::where('id_vehiculo', $vehiculo->id_vehiculo)
                                          ->where('id_sucursal', $sucursal->id_sucursal)
                                          ->first();

            if (!$asignacion) {
                $asignacion = VehiculoSucursal::create([
                    'id_vehiculo' => $vehiculo->id_vehiculo,
                    'id_sucursal' => $sucursal->id_sucursal,
                    'fecha_asignacion' => now(),
                ]);

                event(new VehiculoSucursalAsignado($asignacion->load(['vehiculo', 'sucursal'])));
                $this->line("🔗 Nueva Asignación: {$vehiculo->placa} ↔ {$sucursal->nombre}");
            } else {
                $asignacion->fecha_asignacion = now();
                $asignacion->save();

                event(new VehiculoSucursalActualizado($asignacion->load(['vehiculo', 'sucursal'])));
                $this->line("🔄 Asignación Actualizada: {$vehiculo->placa} ↔ {$sucursal->nombre}");
            }
        }
    }
}
