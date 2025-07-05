import asyncio
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import SessionLocal
from app.db.models.vehiculo import Vehiculo

async def agregar_vehiculos():
    async with SessionLocal() as session:
        vehiculos = [
            Vehiculo(placa="ABC123", marca="Toyota", modelo="Corolla", anio=2020, tipo_id="Sedan", estado="Disponible"),
            Vehiculo(placa="XYZ789", marca="Honda", modelo="Civic", anio=2019, tipo_id="Sedan", estado="Disponible"),
            Vehiculo(placa="JKL456", marca="Ford", modelo="Focus", anio=2021, tipo_id="Hatchback", estado="En mantenimiento"),
        ]
        session.add_all(vehiculos)
        await session.commit()
        print("Vehículos agregados correctamente.")

if __name__ == "__main__":
    asyncio.run(agregar_vehiculos())