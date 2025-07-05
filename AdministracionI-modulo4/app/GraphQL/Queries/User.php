<?php

namespace App\GraphQL\Queries;

use App\Models\User as UserModel;

class User
{
    public function __invoke($root, array $args)
    {
        return UserModel::where('id_usuario', $args['id_usuario'])->first();
    }
}
