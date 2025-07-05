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

class VehiculoEstadoCambiado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vehiculo;
    public $estadoAnterior;

    /**
     * Create a new event instance.
     */
    public function __construct(Vehiculo $vehiculo, $estadoAnterior)
    {
        $this->vehiculo = $vehiculo;
        $this->estadoAnterior = $estadoAnterior;
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
        return 'vehiculo.estado_cambiado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'vehiculo' => $this->vehiculo->toArray(),
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->vehiculo->estado,
            'message' => "Estado del vehículo {$this->vehiculo->placa} cambió de '{$this->estadoAnterior}' a '{$this->vehiculo->estado}'",
            'timestamp' => now()->toISOString(),
        ];
    }
}
