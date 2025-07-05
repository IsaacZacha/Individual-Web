from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from fastapi import HTTPException
from app.db.models.rol import Rol
from app.schemas.rol import RolCreate
from typing import Optional, Sequence
async def get_roles(db: AsyncSession) -> Sequence[Rol]:
    return (await db.execute(select(Rol))).scalars().all()
async def get_rol_by_id(db: AsyncSession, rol_id: int) -> Optional[Rol]:
    return (await db.execute(select(Rol).where(Rol.id_rol == rol_id))).scalars().first()
async def create_rol(db: AsyncSession, data: RolCreate) -> Rol:
    rol = Rol(**data.model_dump())
    db.add(rol)
    await db.commit()
    await db.refresh(rol)
    return rol
async def update_rol(db: AsyncSession, rol_id: int, data: RolCreate) -> Rol:
    rol = await get_rol_by_id(db, rol_id)
    if not rol:
        raise HTTPException(status_code=404, detail="Rol no encontrado")
    for attr, value in data.model_dump().items():
        setattr(rol, attr, value)
    await db.commit()
    await db.refresh(rol)
    return rol
async def delete_rol(db: AsyncSession, rol_id: int) -> dict:
    rol = await get_rol_by_id(db, rol_id)
    if not rol:
        raise HTTPException(status_code=404, detail="Rol no encontrado")
    nombre = rol.nombre
    await db.delete(rol)
    await db.commit()
    return {"mensaje": f"Rol '{nombre}' eliminado correctamente"}