from fastapi import FastAPI, WebSocket, WebSocketDisconnect, HTTPException, Depends
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session
from typing import List
import asyncio
from contextlib import asynccontextmanager

from app.config import settings
from app.database import init_db, close_db, get_db
from app.models import Cliente, Vehiculo, Reserva, Alquiler
from app.services.alquiler_service import ClienteService, VehiculoService, ReservaService
from app.websockets.connection_manager import connection_manager, heartbeat

# Modelos Pydantic para las APIs REST
from pydantic import BaseModel
from datetime import date
from typing import Optional

class ClienteCreate(BaseModel):
    nombre: str
    email: str

class ClienteResponse(BaseModel):
    id: int
    nombre: str
    email: str
    
    class Config:
        from_attributes = True

class VehiculoCreate(BaseModel):
    modelo: str
    placa: str

class VehiculoResponse(BaseModel):
    id: int
    modelo: str
    placa: str
    
    class Config:
        from_attributes = True

class ReservaCreate(BaseModel):
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: date
    fecha_inicio: date
    fecha_fin: date
    estado: str

class ReservaResponse(BaseModel):
    id_reserva: int
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: Optional[date]
    fecha_inicio: Optional[date]
    fecha_fin: Optional[date]
    estado: str
    
    class Config:
        from_attributes = True

# Lifespan manager para inicializar y cerrar recursos
@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    await init_db()
    # Iniciar heartbeat en background
    heartbeat_task = asyncio.create_task(heartbeat())
    
    yield
    
    # Shutdown
    heartbeat_task.cancel()
    await close_db()

# Crear la aplicación FastAPI
app = FastAPI(
    title=settings.app_name,
    version=settings.app_version,
    description="Sistema de Alquiler de Vehículos con APIs REST y WebSockets",
    lifespan=lifespan
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Endpoint de salud
@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "service": settings.app_name,
        "version": settings.app_version
    }

# Endpoint principal
@app.get("/")
async def root():
    return {
        "message": "Sistema de Alquiler de Vehículos API",
        "version": settings.app_version,
        "endpoints": {
            "clientes": "/clientes",
            "vehiculos": "/vehiculos", 
            "reservas": "/reservas",
            "websocket": "/ws/{subscription_type}",
            "health": "/health",
            "docs": "/docs"
        }
    }

# === ENDPOINTS PARA CLIENTES ===
@app.get("/clientes", response_model=List[ClienteResponse])
async def get_clientes(db: Session = Depends(get_db)):
    service = ClienteService(db)
    return service.get_all()

@app.get("/clientes/{cliente_id}", response_model=ClienteResponse)
async def get_cliente(cliente_id: int, db: Session = Depends(get_db)):
    service = ClienteService(db)
    cliente = service.get_by_id(cliente_id)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return cliente

@app.post("/clientes", response_model=ClienteResponse)
async def create_cliente(cliente: ClienteCreate, db: Session = Depends(get_db)):
    service = ClienteService(db)
    return service.create(cliente)

# === ENDPOINTS PARA VEHÍCULOS ===
@app.get("/vehiculos", response_model=List[VehiculoResponse])
async def get_vehiculos(db: Session = Depends(get_db)):
    service = VehiculoService(db)
    return service.get_all()

@app.get("/vehiculos/{vehiculo_id}", response_model=VehiculoResponse)
async def get_vehiculo(vehiculo_id: int, db: Session = Depends(get_db)):
    service = VehiculoService(db)
    vehiculo = service.get_by_id(vehiculo_id)
    if not vehiculo:
        raise HTTPException(status_code=404, detail="Vehículo no encontrado")
    return vehiculo

@app.post("/vehiculos", response_model=VehiculoResponse)
async def create_vehiculo(vehiculo: VehiculoCreate, db: Session = Depends(get_db)):
    service = VehiculoService(db)
    return service.create(vehiculo)

# === ENDPOINTS PARA RESERVAS ===
@app.get("/reservas", response_model=List[ReservaResponse])
async def get_reservas(db: Session = Depends(get_db)):
    service = ReservaService(db)
    return service.get_all()

@app.get("/reservas/{reserva_id}", response_model=ReservaResponse)
async def get_reserva(reserva_id: int, db: Session = Depends(get_db)):
    service = ReservaService(db)
    reserva = service.get_by_id(reserva_id)
    if not reserva:
        raise HTTPException(status_code=404, detail="Reserva no encontrada")
    return reserva

@app.post("/reservas", response_model=ReservaResponse)
async def create_reserva(reserva: ReservaCreate, db: Session = Depends(get_db)):
    service = ReservaService(db)
    new_reserva = service.create(reserva)
    
    # Notificar por WebSocket
    await connection_manager.broadcast_to_subscription(
        {
            "type": "nueva_reserva",
            "data": {
                "id_reserva": new_reserva.id_reserva,
                "cliente_id": new_reserva.cliente_id,
                "vehiculo_id": new_reserva.vehiculo_id,
                "estado": new_reserva.estado
            },
            "message": f"Nueva reserva creada para el cliente {new_reserva.cliente_id}"
        },
        "reservas"
    )
    
    return new_reserva

# === WEBSOCKETS ===
@app.websocket("/ws/{subscription_type}")
async def websocket_endpoint(websocket: WebSocket, subscription_type: str):
    await connection_manager.connect(websocket, subscription_type)
    try:
        while True:
            data = await websocket.receive_text()
            response = {
                "type": "echo",
                "received": data,
                "subscription": subscription_type
            }
            await connection_manager.send_personal_message(response, websocket)
            
    except WebSocketDisconnect:
        connection_manager.disconnect(websocket)

@app.websocket("/ws")
async def websocket_general(websocket: WebSocket):
    await websocket_endpoint(websocket, "general")

# === ESTADÍSTICAS ===
@app.get("/ws/stats")
async def websocket_stats():
    return {
        "active_connections": connection_manager.get_connection_stats(),
        "total_connections": sum(connection_manager.get_connection_stats().values())
    }

# === TESTING ===
@app.get("/test/notification/{notification_type}")
async def test_notification(notification_type: str):
    test_data = {
        "id": 999,
        "test": True,
        "message": f"Notificación de prueba: {notification_type}"
    }
    
    await connection_manager.broadcast_to_subscription(
        {
            "type": f"test_{notification_type}",
            "data": test_data,
            "message": f"Prueba de {notification_type}"
        },
        notification_type if notification_type in ["reservas", "alquileres", "pagos"] else "general"
    )
    
    return {"message": f"Notificación {notification_type} enviada", "data": test_data}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.simple_main:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )
