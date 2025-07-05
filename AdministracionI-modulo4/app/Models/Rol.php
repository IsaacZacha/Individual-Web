<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\RolCreado;
use App\Events\RolActualizado;
use App\Events\RolEliminado;

/**
 * Modelo Rol
 *
 * @property int $id_rol
 * @property string $nombre
 *
 * @example {
 *   "id_rol": 1,
 *   "nombre": "Administrador"
 * }
 */
class Rol extends Model
{
    use HasFactory;
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = true;
    protected $fillable = ['nombre', 'descripcion'];
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_rol';
    }
    
    /**
     * Eventos WebSocket para el modelo
     */
    protected $dispatchesEvents = [
        'created' => RolCreado::class,
        'updated' => RolActualizado::class,
        'deleted' => RolEliminado::class,
    ];

    /**
     * Relación con Users (Un rol puede tener múltiples usuarios)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id', 'id_rol');
    }
}