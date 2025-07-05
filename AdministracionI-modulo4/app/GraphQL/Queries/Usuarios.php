<?php

namespace App\GraphQL\Queries;

use App\Models\User as UserModel;

class Usuarios
{
    public function __invoke($root, array $args)
    {
        return UserModel::all();
    }
}
