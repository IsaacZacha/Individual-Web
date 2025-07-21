from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.database import engine, Base
from app.controllers import (
    cliente_router,
    vehiculo_router,
    reserva_router,
    alquiler_router,
    pago_router,
    multa_router,
    inspeccion_router
)

# Crear las tablas
Base.metadata.create_all(bind=engine)

# Crear la aplicación FastAPI
app = FastAPI(
    title="Sistema de Alquiler de Vehículos",
    description="API para gestión de alquiler de vehículos",
    version="1.0.0"
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Registrar los routers
app.include_router(cliente_router)
app.include_router(vehiculo_router)
app.include_router(reserva_router)
app.include_router(alquiler_router)
app.include_router(pago_router)
app.include_router(multa_router)
app.include_router(inspeccion_router)


@app.get("/")
def read_root():
    return {
        "message": "Bienvenido al Sistema de Alquiler de Vehículos",
        "version": "1.0.0",
        "docs": "/docs"
    }


@app.get("/health")
def health_check():
    return {"status": "healthy"}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
