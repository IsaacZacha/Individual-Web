<?php

namespace App\Events;

use App\Models\Rol;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RolEliminado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rol;

    public function __construct(Rol $rol)
    {
        $this->rol = $rol;
    }

    public function broadcastOn()
    {
        return [
            new Channel('roles'),
            new Channel('dashboard')
        ];
    }

    public function broadcastAs()
    {
        return 'rol.eliminado';
    }

    public function broadcastWith()
    {
        return [
            'rol' => $this->rol->toArray(),
            'message' => "Rol eliminado: {$this->rol->nombre}",
            'timestamp' => now()->toISOString(),
            'entity_type' => 'rol',
            'action' => 'deleted',
            'type' => 'rol.eliminado'
        ];
    }
}
