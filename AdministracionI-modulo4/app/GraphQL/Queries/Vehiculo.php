<?php

namespace App\GraphQL\Queries;

use App\Models\Vehiculo as VehiculoModel;

class Vehiculo
{
    public function __invoke($root, array $args)
    {
        return VehiculoModel::where('id_vehiculo', $args['id_vehiculo'])->first();
    }
}
