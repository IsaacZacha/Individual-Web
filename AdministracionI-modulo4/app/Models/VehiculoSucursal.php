<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\VehiculoSucursalAsignado;
use App\Events\VehiculoSucursalActualizado;
use App\Events\VehiculoSucursalDesasignado;

/**
 * Modelo VehiculoSucursal
 *
 * @property int $id
 * @property int $id_vehiculo
 * @property int $id_sucursal
 * @property string $fecha_asignacion
 *
 * @example {
 *   "id": 1,
 *   "id_vehiculo": 1,
 *   "id_sucursal": 2,
 *   "fecha_asignacion": "2024-07-01"
 * }
 */
class VehiculoSucursal extends Model
{
    use HasFactory;
    protected $table = 'vehiculo_sucursal';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['id_vehiculo', 'id_sucursal', 'fecha_asignacion'];
    
    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'id_vehiculo' => 'integer',
        'id_sucursal' => 'integer',
    ];

    protected $dispatchesEvents = [
        'created' => VehiculoSucursalAsignado::class,
        'updated' => VehiculoSucursalActualizado::class,
        'deleted' => VehiculoSucursalDesasignado::class,
    ];

    /**
     * Relación con Vehiculo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    /**
     * Relación con Sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }
}