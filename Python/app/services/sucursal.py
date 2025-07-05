from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from fastapi import HTTPException
from app.db.models.sucursal import Sucursal
from app.schemas.sucursal import SucursalCreate
from typing import Optional, Sequence

async def get_sucursales(db: AsyncSession) -> Sequence[Sucursal]:
    return (await db.execute(select(Sucursal))).scalars().all()

async def get_sucursal_by_id(db: AsyncSession, sucursal_id: int) -> Optional[Sucursal]:
    return (await db.execute(select(Sucursal).where(Sucursal.id_sucursal == sucursal_id))).scalars().first()

async def create_sucursal(db: AsyncSession, data: SucursalCreate) -> Sucursal:
    sucursal = Sucursal(**data.model_dump())
    db.add(sucursal)
    await db.commit()
    await db.refresh(sucursal)
    return sucursal

async def update_sucursal(db: AsyncSession, sucursal_id: int, data: SucursalCreate) -> Sucursal:
    sucursal = await get_sucursal_by_id(db, sucursal_id)
    if not sucursal:
        raise HTTPException(status_code=404, detail="Sucursal no encontrada")
    for attr, value in data.model_dump().items():
        setattr(sucursal, attr, value)
    await db.commit()
    await db.refresh(sucursal)
    return sucursal

async def delete_sucursal(db: AsyncSession, sucursal_id: int) -> dict:
    sucursal = await get_sucursal_by_id(db, sucursal_id)
    if not sucursal:
        raise HTTPException(status_code=404, detail="Sucursal no encontrada")
    nombre = sucursal.nombre
    await db.delete(sucursal)
    await db.commit()
    return {"mensaje": f"Sucursal '{nombre}' eliminada correctamente"}