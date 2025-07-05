<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehiculo;
use App\Models\Sucursal;
use App\Models\VehiculoSucursal;
use App\Events\VehiculoSucursalAsignado;
use App\Events\VehiculoSucursalDesasignado;
use App\Events\VehiculoSucursalActualizado;

class TestAsignaciones extends Command
{
    protected $signature = 'test:asignaciones {--count=5 : Número de asignaciones a generar}';
    protected $description = 'Generar eventos específicos de asignaciones vehiculo-sucursal para testing';

    public function handle()
    {
        $count = $this->option('count');
        
        $this->info("🔗 Generando {$count} eventos de asignaciones vehículo-sucursal...");

        for ($i = 1; $i <= $count; $i++) {
            $this->info("📡 Asignación {$i}/{$count}");

            $this->createRandomAsignacion();

            // Esperar 3 segundos entre eventos para mejor visualización
            sleep(3);
        }

        $this->info("✅ Eventos de asignaciones generados correctamente");
        $this->info("📊 Total asignaciones actuales: " . VehiculoSucursal::count());
    }

    private function createRandomAsignacion()
    {
        $vehiculos = Vehiculo::all();
        $sucursales = Sucursal::all();

        if ($vehiculos->isEmpty() || $sucursales->isEmpty()) {
            $this->error("❌ No hay vehículos o sucursales disponibles");
            return;
        }

        $vehiculo = $vehiculos->random();
        $sucursal = $sucursales->random();

        // Verificar si ya existe la asignación
        $asignacionExistente = VehiculoSucursal::where('id_vehiculo', $vehiculo->id_vehiculo)
                                               ->where('id_sucursal', $sucursal->id_sucursal)
                                               ->first();

        if ($asignacionExistente) {
            // Actualizar asignación existente
            $asignacionExistente->fecha_asignacion = now();
            $asignacionExistente->save();
            $asignacionExistente->load(['vehiculo', 'sucursal']);

            event(new VehiculoSucursalActualizado($asignacionExistente));
            $this->line("🔄 Actualizada: {$vehiculo->placa} ↔ {$sucursal->nombre}");
        } else {
            // Crear nueva asignación
            $nuevaAsignacion = VehiculoSucursal::create([
                'id_vehiculo' => $vehiculo->id_vehiculo,
                'id_sucursal' => $sucursal->id_sucursal,
                'fecha_asignacion' => now(),
            ]);

            $nuevaAsignacion->load(['vehiculo', 'sucursal']);
            event(new VehiculoSucursalAsignado($nuevaAsignacion));
            $this->line("✅ Nueva: {$vehiculo->placa} ↔ {$sucursal->nombre}");
        }
    }
}
