<?php

namespace App\GraphQL\Mutations;

use App\Models\Sucursal;
use App\Events\SucursalCreada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CrearSucursal
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Creando sucursal via GraphQL', ['input' => $args['input']]);
                
                $sucursal = Sucursal::create($args['input']);
                
                Log::info('Sucursal creada exitosamente', [
                    'sucursal_id' => $sucursal->id_sucursal,
                    'nombre' => $sucursal->nombre
                ]);
                
                // Disparar evento
                Event::dispatch(new SucursalCreada($sucursal));
                
                return $sucursal;
            });
        } catch (\Exception $e) {
            Log::error('Error al crear sucursal via GraphQL', [
                'error' => $e->getMessage(),
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
