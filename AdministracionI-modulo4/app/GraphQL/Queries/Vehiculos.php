<?php

namespace App\GraphQL\Queries;

use App\Models\Vehiculo as VehiculoModel;

class Vehiculos
{
    public function __invoke($root, array $args)
    {
        return VehiculoModel::all();
    }
}
