<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreado implements ShouldBroadcast
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
        return 'usuario.creado';
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user->toArray(),
            'message' => "Nuevo usuario creado: {$this->user->username}",
            'timestamp' => now()->toISOString(),
            'entity_type' => 'usuario',
            'action' => 'created',
            'type' => 'usuario.creado'
        ];
    }
}
