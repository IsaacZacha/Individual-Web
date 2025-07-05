<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Events\UserCreado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class CrearUser
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Creando user via GraphQL', ['input' => $args['input']]);
                
                $input = $args['input'];
                $input['password'] = Hash::make($input['password']);
                
                $user = User::create($input);
                
                Log::info('User creado exitosamente', [
                    'user_id' => $user->id_usuario,
                    'username' => $user->username
                ]);
                
                // Disparar evento
                Event::dispatch(new UserCreado($user));
                
                return $user;
            });
        } catch (\Exception $e) {
            Log::error('Error al crear user via GraphQL', [
                'error' => $e->getMessage(),
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
