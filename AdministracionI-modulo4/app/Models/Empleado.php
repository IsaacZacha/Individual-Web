<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Empleado
 *
 * @property int $id_empleado
 * @property string $nombre
 * @property string $cargo
 * @property string $correo
 * @property string $telefono
 *
 * @example {
 *   "id_empleado": 1,
 *   "nombre": "Juan Pérez",
 *   "cargo": "Gerente",
 *   "correo": "juan@empresa.com",
 *   "telefono": "0999999999"
 * }
 */
class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleado';
    protected $primaryKey = 'id_empleado';
    public $incrementing = true;
    protected $fillable = ['nombre', 'cargo', 'correo', 'telefono'];
    public $timestamps = true;

    /**
     * Relación con User (Un empleado puede tener un usuario)
     */
    public function user()
    {
        return $this->hasOne(User::class, 'empleado_id', 'id_empleado');
    }
}