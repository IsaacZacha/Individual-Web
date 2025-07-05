<?php

namespace App\GraphQL\Queries;

use App\Models\Sucursal as SucursalModel;

class Sucursal
{
    public function __invoke($root, array $args)
    {
        return SucursalModel::where('id_sucursal', $args['id_sucursal'])->first();
    }
}
