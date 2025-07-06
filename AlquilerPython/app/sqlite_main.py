from fastapi import FastAPI, WebSocket, WebSocketDisconnect, HTTPException, Depends
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session
from typing import List
import asyncio
from contextlib import asynccontextmanager

from app.config_sqlite import settings
from app.database_sqlite import init_db, close_db, get_db
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
    title=settings.app_name + " (SQLite)",
    version=settings.app_version,
    description="Sistema de Alquiler de Vehículos con APIs REST y WebSockets - Versión SQLite",
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
        "version": settings.app_version,
        "database": "SQLite (Development)"
    }

# Endpoint principal
@app.get("/")
async def root():
    return {
        "message": "🚗 Sistema de Alquiler de Vehículos API - Versión SQLite",
        "version": settings.app_version,
        "database": "SQLite (Development)",
        "endpoints": {
            "clientes": "/clientes",
            "vehiculos": "/vehiculos", 
            "reservas": "/reservas",
            "websocket": "/ws/{subscription_type}",
            "health": "/health",
            "docs": "/docs"
        },
        "features": [
            "✅ APIs REST completas",
            "✅ WebSockets en tiempo real",
            "✅ Base de datos SQLite",
            "✅ Documentación automática",
            "✅ CORS habilitado",
            "✅ Notificaciones en tiempo real"
        ]
    }

# === ENDPOINTS PARA CLIENTES ===
@app.get("/clientes", response_model=List[ClienteResponse])
async def get_clientes(db: Session = Depends(get_db)):
    """Obtener todos los clientes"""
    service = ClienteService(db)
    return service.get_all()

@app.get("/clientes/{cliente_id}", response_model=ClienteResponse)
async def get_cliente(cliente_id: int, db: Session = Depends(get_db)):
    """Obtener un cliente por ID"""
    service = ClienteService(db)
    cliente = service.get_by_id(cliente_id)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return cliente

@app.post("/clientes", response_model=ClienteResponse)
async def create_cliente(cliente: ClienteCreate, db: Session = Depends(get_db)):
    """Crear un nuevo cliente"""
    service = ClienteService(db)
    nuevo_cliente = service.create(cliente)
    
    # Notificar por WebSocket
    await connection_manager.broadcast_to_subscription(
        {
            "type": "nuevo_cliente",
            "data": {
                "id": nuevo_cliente.id,
                "nombre": nuevo_cliente.nombre,
                "email": nuevo_cliente.email
            },
            "message": f"Nuevo cliente registrado: {nuevo_cliente.nombre}"
        },
        "clientes"
    )
    
    return nuevo_cliente

# === ENDPOINTS PARA VEHÍCULOS ===
@app.get("/vehiculos", response_model=List[VehiculoResponse])
async def get_vehiculos(db: Session = Depends(get_db)):
    """Obtener todos los vehículos"""
    service = VehiculoService(db)
    return service.get_all()

@app.get("/vehiculos/{vehiculo_id}", response_model=VehiculoResponse)
async def get_vehiculo(vehiculo_id: int, db: Session = Depends(get_db)):
    """Obtener un vehículo por ID"""
    service = VehiculoService(db)
    vehiculo = service.get_by_id(vehiculo_id)
    if not vehiculo:
        raise HTTPException(status_code=404, detail="Vehículo no encontrado")
    return vehiculo

@app.post("/vehiculos", response_model=VehiculoResponse)
async def create_vehiculo(vehiculo: VehiculoCreate, db: Session = Depends(get_db)):
    """Crear un nuevo vehículo"""
    service = VehiculoService(db)
    nuevo_vehiculo = service.create(vehiculo)
    
    # Notificar por WebSocket
    await connection_manager.broadcast_to_subscription(
        {
            "type": "nuevo_vehiculo",
            "data": {
                "id": nuevo_vehiculo.id,
                "modelo": nuevo_vehiculo.modelo,
                "placa": nuevo_vehiculo.placa
            },
            "message": f"Nuevo vehículo agregado: {nuevo_vehiculo.modelo}"
        },
        "vehiculos"
    )
    
    return nuevo_vehiculo

# === ENDPOINTS PARA RESERVAS ===
@app.get("/reservas", response_model=List[ReservaResponse])
async def get_reservas(db: Session = Depends(get_db)):
    """Obtener todas las reservas"""
    service = ReservaService(db)
    return service.get_all()

@app.get("/reservas/{reserva_id}", response_model=ReservaResponse)
async def get_reserva(reserva_id: int, db: Session = Depends(get_db)):
    """Obtener una reserva por ID"""
    service = ReservaService(db)
    reserva = service.get_by_id(reserva_id)
    if not reserva:
        raise HTTPException(status_code=404, detail="Reserva no encontrada")
    return reserva

@app.post("/reservas", response_model=ReservaResponse)
async def create_reserva(reserva: ReservaCreate, db: Session = Depends(get_db)):
    """Crear una nueva reserva"""
    service = ReservaService(db)
    nueva_reserva = service.create(reserva)
    
    # Notificar por WebSocket
    await connection_manager.broadcast_to_subscription(
        {
            "type": "nueva_reserva",
            "data": {
                "id_reserva": nueva_reserva.id_reserva,
                "cliente_id": nueva_reserva.cliente_id,
                "vehiculo_id": nueva_reserva.vehiculo_id,
                "estado": nueva_reserva.estado,
                "fecha_inicio": str(nueva_reserva.fecha_inicio),
                "fecha_fin": str(nueva_reserva.fecha_fin)
            },
            "message": f"Nueva reserva creada para el cliente {nueva_reserva.cliente_id}"
        },
        "reservas"
    )
    
    return nueva_reserva

# === WEBSOCKETS ===
@app.websocket("/ws/{subscription_type}")
async def websocket_endpoint(websocket: WebSocket, subscription_type: str):
    """Conectar a WebSocket con tipo de suscripción específico"""
    await connection_manager.connect(websocket, subscription_type)
    try:
        while True:
            data = await websocket.receive_text()
            response = {
                "type": "echo",
                "received": data,
                "subscription": subscription_type,
                "timestamp": str(asyncio.get_event_loop().time())
            }
            await connection_manager.send_personal_message(response, websocket)
            
    except WebSocketDisconnect:
        connection_manager.disconnect(websocket)

@app.websocket("/ws")
async def websocket_general(websocket: WebSocket):
    """Conectar a WebSocket general"""
    await websocket_endpoint(websocket, "general")

# === ESTADÍSTICAS ===
@app.get("/ws/stats")
async def websocket_stats():
    """Obtener estadísticas de conexiones WebSocket"""
    return {
        "active_connections": connection_manager.get_connection_stats(),
        "total_connections": sum(connection_manager.get_connection_stats().values()),
        "subscription_types": list(connection_manager.active_connections.keys())
    }

# === TESTING Y DEMO ===
@app.get("/test/notification/{notification_type}")
async def test_notification(notification_type: str):
    """Enviar notificación de prueba"""
    test_data = {
        "id": 999,
        "test": True,
        "message": f"Notificación de prueba: {notification_type}",
        "timestamp": str(asyncio.get_event_loop().time())
    }
    
    await connection_manager.broadcast_to_subscription(
        {
            "type": f"test_{notification_type}",
            "data": test_data,
            "message": f"🧪 Prueba de {notification_type}"
        },
        notification_type if notification_type in ["reservas", "alquileres", "pagos", "clientes", "vehiculos"] else "general"
    )
    
    return {"message": f"✅ Notificación {notification_type} enviada", "data": test_data}

@app.get("/demo/data")
async def create_demo_data(db: Session = Depends(get_db)):
    """Crear datos de demostración"""
    try:
        # Crear clientes de demo
        cliente_service = ClienteService(db)
        vehiculo_service = VehiculoService(db)
        
        clientes = [
            ClienteCreate(nombre="Juan Pérez", email="juan@email.com"),
            ClienteCreate(nombre="María García", email="maria@email.com"),
            ClienteCreate(nombre="Carlos López", email="carlos@email.com")
        ]
        
        vehiculos = [
            VehiculoCreate(modelo="Toyota Corolla 2023", placa="ABC-123"),
            VehiculoCreate(modelo="Honda Civic 2022", placa="DEF-456"),
            VehiculoCreate(modelo="Nissan Sentra 2023", placa="GHI-789")
        ]
        
        clientes_creados = []
        vehiculos_creados = []
        
        for cliente_data in clientes:
            cliente = cliente_service.create(cliente_data)
            clientes_creados.append(cliente)
        
        for vehiculo_data in vehiculos:
            vehiculo = vehiculo_service.create(vehiculo_data)
            vehiculos_creados.append(vehiculo)
        
        return {
            "message": "✅ Datos de demostración creados exitosamente",
            "clientes": len(clientes_creados),
            "vehiculos": len(vehiculos_creados)
        }
    
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Error creando datos: {str(e)}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.sqlite_main:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )
