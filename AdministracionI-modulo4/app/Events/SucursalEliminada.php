<?php

namespace App\Events;

use App\Models\Sucursal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SucursalEliminada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sucursalId;

    public function __construct($sucursalId)
    {
        $this->sucursalId = $sucursalId;
    }

    public function broadcastOn()
    {
        return [
            new Channel('sucursales'),
            new Channel('dashboard'),
        ];
    }

    public function broadcastAs()
    {
        return 'sucursal.eliminada';
    }

    public function broadcastWith()
    {
        return [
            'sucursal_id' => $this->sucursalId,
            'message' => "Sucursal eliminada (ID: {$this->sucursalId})",
            'timestamp' => now()->toISOString(),
        ];
    }
}
