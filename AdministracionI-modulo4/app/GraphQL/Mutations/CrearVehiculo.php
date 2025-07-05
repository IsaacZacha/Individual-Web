<?php

namespace App\GraphQL\Mutations;

use App\Models\Vehiculo;
use App\Events\VehiculoCreado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CrearVehiculo
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Creando vehiculo via GraphQL', ['input' => $args['input']]);
                
                $vehiculo = Vehiculo::create($args['input']);
                
                Log::info('Vehiculo creado exitosamente', [
                    'vehiculo_id' => $vehiculo->id_vehiculo,
                    'placa' => $vehiculo->placa
                ]);
                
                // Disparar evento
                Event::dispatch(new VehiculoCreado($vehiculo));
                
                return $vehiculo;
            });
        } catch (\Exception $e) {
            Log::error('Error al crear vehiculo via GraphQL', [
                'error' => $e->getMessage(),
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
