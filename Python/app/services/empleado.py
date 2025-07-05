from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from fastapi import HTTPException
from app.db.models.empleado import Empleado
from app.schemas.empleado import EmpleadoCreate
from typing import Optional

async def get_empleados(db: AsyncSession):
    result = await db.execute(select(Empleado))
    return result.scalars().all()

async def get_empleado_by_id(db: AsyncSession, empleado_id: int) -> Optional[Empleado]:
    result = await db.execute(select(Empleado).where(Empleado.id_empleado == empleado_id))
    return result.scalars().first()

async def get_empleado_by_correo(db: AsyncSession, correo: str) -> Optional[Empleado]:
    result = await db.execute(select(Empleado).where(Empleado.correo == correo))
    return result.scalars().first()

async def create_empleado(db: AsyncSession, empleado: EmpleadoCreate):
    if await get_empleado_by_correo(db, empleado.correo) is not None:
        raise HTTPException(status_code=400, detail="El correo debe ser único")
    nuevo = Empleado(**empleado.model_dump())
    db.add(nuevo)
    await db.commit()
    await db.refresh(nuevo)
    return nuevo
async def update_empleado(db: AsyncSession, empleado_id: int, data: EmpleadoCreate):
    empleado = await get_empleado_by_id(db, empleado_id)
    if not empleado:
        raise HTTPException(status_code=404, detail="Empleado no encontrado")
    # Validar correo único si cambia
    correo_actual = getattr(empleado, "correo", None)
    correo_nuevo = data.correo
    correo_existente = await get_empleado_by_correo(db, correo_nuevo)
    if (correo_actual is not None and correo_nuevo is not None and
        correo_actual != correo_nuevo and correo_existente is not None):
        raise HTTPException(status_code=400, detail="El correo debe ser único")
    for attr, value in data.model_dump().items():
        setattr(empleado, attr, value)
    await db.commit()
    await db.refresh(empleado)
    return empleado

async def delete_empleado(db: AsyncSession, empleado_id: int) -> dict:
    empleado = await get_empleado_by_id(db, empleado_id)
    if not empleado:
        raise HTTPException(status_code=404, detail="Empleado no encontrado")
    nombre = empleado.nombre 
    await db.delete(empleado)
    await db.commit()
    return {"mensaje": f"Empleado '{nombre}' eliminado correctamente"}