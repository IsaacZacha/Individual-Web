from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from fastapi import HTTPException
from app.db.models.vehiculo_sucursal import VehiculoSucursal
from app.db.models.vehiculo import Vehiculo
from app.db.models.sucursal import Sucursal
from app.schemas.vehiculo_sucursal import VehiculoSucursalCreate

async def get_vehiculos_sucursal(db: AsyncSession):
    result = await db.execute(select(VehiculoSucursal))
    return result.scalars().all()

async def get_vehiculo_sucursal_by_id(db: AsyncSession, id_relacion: int):
    result = await db.execute(select(VehiculoSucursal).where(VehiculoSucursal.id_relacion == id_relacion))
    return result.scalars().first()

async def create_vehiculo_sucursal(db: AsyncSession, data: VehiculoSucursalCreate):
    # Validar que el vehículo exista
    vehiculo = await db.execute(select(Vehiculo).where(Vehiculo.id_vehiculo == data.vehiculo_id))
    if not vehiculo.scalars().first():
        raise HTTPException(status_code=400, detail="El vehículo no existe")
    # Validar que la sucursal exista
    sucursal = await db.execute(select(Sucursal).where(Sucursal.id_sucursal == data.sucursal_id))
    if not sucursal.scalars().first():
        raise HTTPException(status_code=400, detail="La sucursal no existe")
    relacion = VehiculoSucursal(**data.model_dump())
    db.add(relacion)
    await db.commit()
    await db.refresh(relacion)
    return relacion

async def update_vehiculo_sucursal(db: AsyncSession, id_relacion: int, data: VehiculoSucursalCreate):
    relacion = await get_vehiculo_sucursal_by_id(db, id_relacion)
    if not relacion:
        raise HTTPException(status_code=404, detail="Relación no encontrada")
    # Validar que el vehículo exista
    vehiculo = await db.execute(select(Vehiculo).where(Vehiculo.id_vehiculo == data.vehiculo_id))
    if not vehiculo.scalars().first():
        raise HTTPException(status_code=400, detail="El vehículo no existe")
    # Validar que la sucursal exista
    sucursal = await db.execute(select(Sucursal).where(Sucursal.id_sucursal == data.sucursal_id))
    if not sucursal.scalars().first():
        raise HTTPException(status_code=400, detail="La sucursal no existe")
    relacion.vehiculo_id = data.vehiculo_id  # type: ignore
    relacion.sucursal_id = data.sucursal_id  # type: ignore
    relacion.fecha_ingreso = data.fecha_ingreso  # type: ignore
    await db.commit()
    await db.refresh(relacion)
    return relacion

async def delete_vehiculo_sucursal(db: AsyncSession, id_relacion: int) -> dict:
    relacion = await get_vehiculo_sucursal_by_id(db, id_relacion)
    if not relacion:
        raise HTTPException(status_code=404, detail="Vehiculo de Sucursal no encontrado")
    await db.delete(relacion)
    await db.commit()
    return {"mensaje": "Vehículo de sucursal eliminado correctamente"}