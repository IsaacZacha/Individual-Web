<?php

namespace App\GraphQL\Queries;

use App\Models\Sucursal as SucursalModel;

class Sucursales
{
    public function __invoke($root, array $args)
    {
        return SucursalModel::all();
    }
}
