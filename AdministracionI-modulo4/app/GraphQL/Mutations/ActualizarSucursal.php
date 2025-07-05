<?php

namespace App\GraphQL\Mutations;

use App\Models\Sucursal;
use App\Events\SucursalActualizada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ActualizarSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Actualizando sucursal via GraphQL', [
                    'sucursal_id' => $args['id_sucursal'],
                    'input' => $args['input']
                ]);
                
                $sucursal = Sucursal::where('id_sucursal', $args['id_sucursal'])->firstOrFail();
                $sucursal->update($args['input']);
                
                Log::info('Sucursal actualizada exitosamente', [
                    'sucursal_id' => $sucursal->id_sucursal,
                    'nombre' => $sucursal->nombre
                ]);
                
                // Disparar evento
                Event::dispatch(new SucursalActualizada($sucursal));
                
                return $sucursal;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'sucursal_id' => $args['id_sucursal'],
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
