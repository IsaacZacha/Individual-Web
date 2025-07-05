<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserEliminado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userData;

    public function __construct(User $user)
    {
        // Guardamos los datos del usuario antes de que sea eliminado
        $this->userData = [
            'id_usuario' => $user->id_usuario,
            'username' => $user->username,
            'empleado_id' => $user->empleado_id,
            'rol_id' => $user->rol_id,
            'deleted_at' => now()->toISOString()
        ];
    }

    public function broadcastOn()
    {
        return [
            new Channel('usuarios'),
            new Channel('dashboard')
        ];
    }

    public function broadcastAs()
    {
        return 'usuario.eliminado';
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->userData,
            'message' => "Usuario eliminado: {$this->userData['username']}",
            'timestamp' => now()->toISOString(),
            'entity_type' => 'usuario',
            'action' => 'deleted',
            'type' => 'usuario.eliminado'
        ];
    }
}
