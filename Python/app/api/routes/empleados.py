from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import get_db  
from app.schemas.mensaje import MensajeOut
from app.schemas.empleado import EmpleadoCreate, EmpleadoOut
from app.services.empleado import (
    get_empleados,
    get_empleado_by_id,
    create_empleado,
    update_empleado,
    delete_empleado,
)
from app.api.routes.auth import get_current_user
router = APIRouter()

@router.get("/", response_model=list[EmpleadoOut])
async def listar_empleados(
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await get_empleados(db)

@router.get("/{empleado_id}", response_model=EmpleadoOut)
async def obtener_empleado(
    empleado_id: int,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    empleado = await get_empleado_by_id(db, empleado_id)
    if not empleado:
        raise HTTPException(status_code=404, detail="Empleado no encontrado")
    return empleado

@router.post("/", response_model=EmpleadoOut)
async def crear_empleado(
    data: EmpleadoCreate,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await create_empleado(db, data)

@router.put("/{empleado_id}", response_model=EmpleadoOut)
async def actualizar_empleado(
    empleado_id: int,
    data: EmpleadoCreate,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await update_empleado(db, empleado_id, data)
@router.delete("/{empleado_id}", response_model=MensajeOut)
async def eliminar_empleado(
    empleado_id: int,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await delete_empleado(db, empleado_id)