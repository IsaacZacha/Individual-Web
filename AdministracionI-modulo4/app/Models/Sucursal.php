<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Sucursal
 *
 * @property int $id_sucursal
 * @property string $nombre
 * @property string $direccion
 * @property string $ciudad
 * @property string $telefono
 *
 * @example {
 *   "id_sucursal": 1,
 *   "nombre": "Sucursal Norte",
 *   "direccion": "Av. Principal 123",
 *   "ciudad": "Quito",
 *   "telefono": "022345678"
 * }
 */
class Sucursal extends Model
{
    use HasFactory;
    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';
    public $incrementing = true;
    protected $fillable = ['nombre', 'direccion', 'ciudad', 'telefono'];

    public function getRouteKeyName()
    {
        return 'id_sucursal';
    }

    /**
     * Relación con VehiculoSucursal (Una sucursal puede tener múltiples vehículos asignados)
     */
    public function vehiculos()
    {
        return $this->hasMany(VehiculoSucursal::class, 'id_sucursal', 'id_sucursal');
    }
}