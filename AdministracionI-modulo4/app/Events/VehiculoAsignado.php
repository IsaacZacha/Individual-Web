<?php

namespace App\Events;

use App\Models\VehiculoSucursal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class VehiculoAsignado implements ShouldBroadcast
{
    use Dispatchable;

    public $asignacion;

    /**
     * Create a new event instance.
     */
    public function __construct(VehiculoSucursal $asignacion)
    {
        $this->asignacion = $asignacion;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('asignaciones'),
        ];
    }

    /**
     * Get the data to broadcast (for testing compatibility).
     */
    public function broadcastWith(): array
    {
        return [
            'asignacion' => $this->asignacion->toArray(),
            'message' => "Vehículo asignado a sucursal",
            'timestamp' => now()->toISOString(),
        ];
    }
}
