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

class SucursalActualizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sucursal;

    /**
     * Create a new event instance.
     */
    public function __construct(Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('sucursales'),
            new Channel('dashboard'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'sucursal.actualizada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'sucursal' => $this->sucursal->toArray(),
            'message' => "Sucursal actualizada: {$this->sucursal->nombre} ({$this->sucursal->ciudad})",
            'timestamp' => now()->toISOString(),
        ];
    }
}
