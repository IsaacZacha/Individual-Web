<?php

namespace App\GraphQL\Queries;

use App\Models\VehiculoSucursal as VehiculoSucursalModel;

class VehiculoSucursales
{
    public function __invoke($root, array $args)
    {
        return VehiculoSucursalModel::all();
    }
}
