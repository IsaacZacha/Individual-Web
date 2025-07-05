<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\VehiculoSucursal;

class VehiculoSucursalAsignado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vehiculoSucursal;

    public function __construct(VehiculoSucursal $vehiculoSucursal)
    {
        $this->vehiculoSucursal = $vehiculoSucursal;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('vehiculo-sucursal'),
            new Channel('dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'vehiculo-sucursal.creado';
    }

    public function broadcastWith(): array
    {
        return [
            'vehiculo_sucursal' => $this->vehiculoSucursal->toArray(),
            'vehiculo' => $this->vehiculoSucursal->vehiculo->toArray(),
            'sucursal' => $this->vehiculoSucursal->sucursal->toArray(),
            'message' => "Vehículo {$this->vehiculoSucursal->vehiculo->placa} asignado a {$this->vehiculoSucursal->sucursal->nombre}",
            'timestamp' => now()->toISOString(),
            'entity_type' => 'vehiculo_sucursal',
            'action' => 'created',
            'type' => 'vehiculo-sucursal.creado'
        ];
    }
}
