from fastapi import APIRouter
from app.api.routes import empleados, sucursales, vehiculos_sucursal, roles, usuarios, auth
from app.api.routes.ia import router as ia_router


api_router = APIRouter()
api_router.include_router(ia_router, prefix="/ia", tags=["IA"])
api_router.include_router(empleados.router, prefix="/empleados", tags=["Empleados"])
api_router.include_router(sucursales.router, prefix="/sucursales", tags=["Sucursales"])
api_router.include_router(vehiculos_sucursal.router, prefix="/vehiculos-sucursal", tags=["VehiculosSucursal"])
api_router.include_router(roles.router, prefix="/roles", tags=["Roles"])
api_router.include_router(usuarios.router, prefix="/usuarios", tags=["Usuarios"])
api_router.include_router(auth.router, prefix="/auth", tags=["Auth"])
