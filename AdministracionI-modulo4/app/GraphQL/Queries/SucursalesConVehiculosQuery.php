<?php

namespace App\GraphQL\Queries;

use App\Models\Sucursal;

class SucursalesConVehiculosQuery
{
    /**
     * Resolver para obtener sucursales con sus vehículos asignados
     */
    public function resolve($rootValue, array $args, $context, $resolveInfo)
    {
        return Sucursal::with(['vehiculos.vehiculo'])
            ->get();
    }
}
