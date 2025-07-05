<?php

namespace App\GraphQL\Mutations;

use App\Models\Vehiculo;
use App\Events\VehiculoActualizado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ActualizarVehiculo
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Actualizando vehiculo via GraphQL', [
                    'vehiculo_id' => $args['id_vehiculo'],
                    'input' => $args['input']
                ]);
                
                $vehiculo = Vehiculo::where('id_vehiculo', $args['id_vehiculo'])->firstOrFail();
                $vehiculo->update($args['input']);
                
                Log::info('Vehiculo actualizado exitosamente', [
                    'vehiculo_id' => $vehiculo->id_vehiculo,
                    'placa' => $vehiculo->placa
                ]);
                
                // Disparar evento
                Event::dispatch(new VehiculoActualizado($vehiculo));
                
                return $vehiculo;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar vehiculo via GraphQL', [
                'error' => $e->getMessage(),
                'vehiculo_id' => $args['id_vehiculo'],
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
