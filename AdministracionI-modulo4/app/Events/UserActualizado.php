<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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
        return 'usuario.actualizado';
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user->toArray(),
            'message' => "Usuario actualizado: {$this->user->username}",
            'timestamp' => now()->toISOString(),
            'entity_type' => 'usuario',
            'action' => 'updated',
            'type' => 'usuario.actualizado'
        ];
    }
}
