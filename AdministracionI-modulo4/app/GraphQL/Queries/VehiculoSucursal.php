<?php

namespace App\GraphQL\Queries;

use App\Models\VehiculoSucursal as VehiculoSucursalModel;

class VehiculoSucursal
{
    public function __invoke($root, array $args)
    {
        return VehiculoSucursalModel::where('id', $args['id'])->first();
    }
}
