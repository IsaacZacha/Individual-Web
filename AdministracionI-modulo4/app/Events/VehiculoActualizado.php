<?php

namespace App\Events;

use App\Models\Vehiculo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehiculoActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vehiculo;

    /**
     * Create a new event instance.
     */
    public function __construct(Vehiculo $vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('vehiculos'),
            new Channel('dashboard'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'vehiculo.actualizado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'vehiculo' => $this->vehiculo->toArray(),
            'message' => "Vehículo actualizado: {$this->vehiculo->marca} {$this->vehiculo->modelo} ({$this->vehiculo->placa})",
            'timestamp' => now()->toISOString(),
        ];
    }
}
