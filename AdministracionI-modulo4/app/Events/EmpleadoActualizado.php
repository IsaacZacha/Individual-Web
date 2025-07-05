<?php

namespace App\Events;

use App\Models\Empleado;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmpleadoActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $empleado;

    /**
     * Create a new event instance.
     */
    public function __construct(Empleado $empleado)
    {
        $this->empleado = $empleado;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('empleados'),
            new Channel('dashboard'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'empleado.actualizado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'empleado' => $this->empleado->toArray(),
            'message' => "Empleado actualizado: {$this->empleado->nombre}",
            'timestamp' => now()->toISOString(),
        ];
    }
}
