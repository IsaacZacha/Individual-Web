<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Events\UserActualizado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ActualizarUser
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Actualizando user via GraphQL', [
                    'user_id' => $args['id_usuario'],
                    'input' => $args['input']
                ]);
                
                $user = User::where('id_usuario', $args['id_usuario'])->firstOrFail();
                $input = $args['input'];
                
                if (isset($input['password'])) {
                    $input['password'] = Hash::make($input['password']);
                }
                
                $user->update($input);
                
                Log::info('User actualizado exitosamente', [
                    'user_id' => $user->id_usuario,
                    'username' => $user->username
                ]);
                
                // Disparar evento
                Event::dispatch(new UserActualizado($user));
                
                return $user;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar user via GraphQL', [
                'error' => $e->getMessage(),
                'user_id' => $args['id_usuario'],
                'input' => $args['input']
            ]);
            throw $e;
        }
    }
}
