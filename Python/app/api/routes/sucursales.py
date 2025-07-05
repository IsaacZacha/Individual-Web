from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import get_db 
from app.schemas.mensaje import MensajeOut
from app.schemas.sucursal import SucursalCreate, SucursalOut
from app.services.sucursal import (
    get_sucursales,
    get_sucursal_by_id,
    create_sucursal,
    update_sucursal,
    delete_sucursal,
)
from app.api.routes.auth import get_current_user

router = APIRouter()

@router.get("/", response_model=list[SucursalOut])
async def listar_sucursales(db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await get_sucursales(db)

@router.get("/{sucursal_id}", response_model=SucursalOut)
async def obtener_sucursal(sucursal_id: int, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    sucursal = await get_sucursal_by_id(db, sucursal_id)
    if not sucursal:
        raise HTTPException(status_code=404, detail="Sucursal no encontrada")
    return sucursal

@router.post("/", response_model=SucursalOut)
async def crear_sucursal(data: SucursalCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await create_sucursal(db, data)

@router.put("/{sucursal_id}", response_model=SucursalOut)
async def actualizar_sucursal(sucursal_id: int, data: SucursalCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await update_sucursal(db, sucursal_id, data)

@router.delete("/{sucursal_id}", response_model=MensajeOut)
async def eliminar_sucursal(sucursal_id: int,db: AsyncSession = Depends(get_db),user=Depends(get_current_user)):
    return await delete_sucursal(db, sucursal_id)