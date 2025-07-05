<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Events\UserEliminado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EliminarUser
{
    public function __invoke($root, array $args)
    {
        try {
            return DB::transaction(function () use ($args) {
                Log::info('Eliminando user via GraphQL', ['user_id' => $args['id_usuario']]);
                
                $user = User::where('id_usuario', $args['id_usuario'])->firstOrFail();
                $userId = $user->id_usuario;
                
                // Disparar evento antes de eliminar
                Event::dispatch(new UserEliminado($user));
                
                $user->delete();
                
                Log::info('User eliminado exitosamente', ['user_id' => $userId]);
                
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente',
                    'deleted_id' => (string) $userId
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al eliminar user via GraphQL', [
                'error' => $e->getMessage(),
                'user_id' => $args['id_usuario']
            ]);
            throw $e;
        }
    }
}
