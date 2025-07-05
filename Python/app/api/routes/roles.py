from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import get_db  
from app.schemas.mensaje import MensajeOut
from app.schemas.rol import RolCreate, RolOut
from app.services.rol import (
    get_roles,
    get_rol_by_id,
    create_rol,
    update_rol,
    delete_rol,
)
from app.api.routes.auth import get_current_user

router = APIRouter()


@router.get("/", response_model=list[RolOut])
async def listar_roles(db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await get_roles(db)

@router.get("/{rol_id}", response_model=RolOut)
async def obtener_rol(rol_id: int, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    rol = await get_rol_by_id(db, rol_id)
    if not rol:
        raise HTTPException(status_code=404, detail="Rol no encontrado")
    return rol

@router.post("/", response_model=RolOut)
async def crear_rol(data: RolCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await create_rol(db, data)

@router.put("/{rol_id}", response_model=RolOut)
async def actualizar_rol(rol_id: int, data: RolCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await update_rol(db, rol_id, data)
@router.delete("/{rol_id}", response_model=MensajeOut)
async def eliminar_rol(
    rol_id: int,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await delete_rol(db, rol_id)