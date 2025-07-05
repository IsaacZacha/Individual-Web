from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import get_db
from app.schemas.mensaje import MensajeOut
from app.schemas.vehiculo_sucursal import VehiculoSucursalCreate, VehiculoSucursalOut
from app.services.vehiculo_sucursal import (
    get_vehiculos_sucursal,
    get_vehiculo_sucursal_by_id,
    create_vehiculo_sucursal,
    update_vehiculo_sucursal,
    delete_vehiculo_sucursal,
)
from app.api.routes.auth import get_current_user
router = APIRouter()
@router.get("/", response_model=list[VehiculoSucursalOut])
async def listar_vehiculos_sucursal(db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await get_vehiculos_sucursal(db)

@router.get("/{id_relacion}", response_model=VehiculoSucursalOut)
async def obtener_vehiculo_sucursal(id_relacion: int, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    relacion = await get_vehiculo_sucursal_by_id(db, id_relacion)
    if not relacion:
        raise HTTPException(status_code=404, detail="Relación no encontrada")
    return relacion

@router.post("/", response_model=VehiculoSucursalOut)
async def crear_vehiculo_sucursal(data: VehiculoSucursalCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await create_vehiculo_sucursal(db, data)

@router.put("/{id_relacion}", response_model=VehiculoSucursalOut)
async def actualizar_vehiculo_sucursal(id_relacion: int, data: VehiculoSucursalCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await update_vehiculo_sucursal(db, id_relacion, data)
@router.delete("/{id_relacion}", response_model=MensajeOut)
async def eliminar_vehiculo_sucursal(
    id_relacion: int,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await delete_vehiculo_sucursal(db, id_relacion)