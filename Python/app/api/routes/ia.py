from fastapi import APIRouter, Depends
from app.services.ia import generar_resumen
from app.services.empleado import get_empleados
from app.services.sucursal import get_sucursales
from app.services.usuario import get_usuarios
from app.services.vehiculo_sucursal import get_vehiculos_sucursal
from app.db.base import get_db

router = APIRouter()

@router.get("/resumen/empleados/")
async def resumen_empleados(db=Depends(get_db)):
    empleados = await get_empleados(db)
    lista = [f"{e.nombre} ({e.cargo})" for e in empleados]
    prompt = (
        "Resume en un párrafo la siguiente lista de empleados, mencionando cantidad, cargos y cualquier patrón relevante:\n"
        + "\n".join(lista)
    )
    resumen = await generar_resumen(prompt)
    return {
        "resumen": resumen,
        "empleados": lista
    }

@router.get("/resumen/sucursales/")
async def resumen_sucursales(db=Depends(get_db)):
    sucursales = await get_sucursales(db)
    lista = [f"{s.nombre} ({s.ciudad})" for s in sucursales]
    prompt = (
        "Resume en un párrafo la siguiente lista de sucursales, mencionando cantidad, ciudades y cualquier patrón relevante:\n"
        + "\n".join(lista)
    )
    resumen = await generar_resumen(prompt, max_tokens=100)
    return {
        "resumen": resumen,
        "sucursales": lista
    }

@router.get("/resumen/usuarios/")
async def resumen_usuarios(db=Depends(get_db)):
    usuarios = await get_usuarios(db)
    lista = [f"{u.username} (rol {u.rol_id})" for u in usuarios]
    prompt = (
        "Resume en un párrafo la siguiente lista de usuarios, mencionando cantidad, roles y cualquier patrón relevante:\n"
        + "\n".join(lista)
    )
    resumen = await generar_resumen(prompt, max_tokens=100)
    return {
        "resumen": resumen,
        "usuarios": lista
    }

@router.get("/resumen/vehiculos-sucursal/")
async def resumen_vehiculos_sucursal(db=Depends(get_db)):
    relaciones = await get_vehiculos_sucursal(db)
    lista = [f"Vehículo {r.vehiculo_id} en sucursal {r.sucursal_id}" for r in relaciones]
    prompt = (
        "Resume en un párrafo la siguiente lista de vehículos asignados a sucursales, mencionando cantidad, sucursales y cualquier patrón relevante:\n"
        + "\n".join(lista)
    )
    resumen = await generar_resumen(prompt, max_tokens=100)
    return {
        "resumen": resumen,
        "vehiculos_sucursal": lista
    }