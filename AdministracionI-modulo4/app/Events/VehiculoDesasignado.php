<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class VehiculoDesasignado implements ShouldBroadcast
{
    use Dispatchable;

    public $asignacionId;

    /**
     * Create a new event instance.
     */
    public function __construct($asignacionId)
    {
        $this->asignacionId = $asignacionId;
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
            'asignacion_id' => $this->asignacionId,
            'message' => "Asignación de vehículo eliminada",
            'timestamp' => now()->toISOString(),
        ];
    }
}
