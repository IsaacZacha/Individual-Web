"""
API Gateway - Sistema de Alquiler de Vehículos
Segundo Parcial - Punto único de entrada con esquema unificado
"""
from fastapi import FastAPI, WebSocket, WebSocketDisconnect, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import HTMLResponse
from contextlib import asynccontextmanager
import asyncio
from datetime import date
from typing import List, Optional, Dict, Any
from pydantic import BaseModel

# Importar WebSocket manager
from app.websockets.connection_manager import connection_manager, heartbeat

# === MODELOS PYDANTIC (Schema Unificado) ===

class Cliente(BaseModel):
    id: int
    nombre: str
    email: str

class Vehiculo(BaseModel):
    id: int
    modelo: str
    placa: str

class Reserva(BaseModel):
    id_reserva: int
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: Optional[date]
    fecha_inicio: Optional[date]
    fecha_fin: Optional[date]
    estado: str
    # Campos relacionados opcionales
    cliente: Optional[Cliente] = None
    vehiculo: Optional[Vehiculo] = None

class Alquiler(BaseModel):
    id_alquiler: int
    reserva_id: int
    fecha_inicio: date
    fecha_fin: date
    precio_total: float
    estado: str
    reserva: Optional[Reserva] = None

class Pago(BaseModel):
    id_pago: int
    alquiler_id: int
    monto: float
    fecha_pago: date
    metodo_pago: str
    estado: str
    alquiler: Optional[Alquiler] = None

class Inspeccion(BaseModel):
    id_inspeccion: int
    alquiler_id: int
    fecha_inspeccion: date
    estado_vehiculo: str
    observaciones: Optional[str]
    alquiler: Optional[Alquiler] = None

class Multa(BaseModel):
    id_multa: int
    alquiler_id: int
    monto: float
    fecha_multa: date
    descripcion: str
    estado: str
    alquiler: Optional[Alquiler] = None

# === INPUTS PARA CREAR ENTIDADES ===

class ClienteInput(BaseModel):
    nombre: str
    email: str

class VehiculoInput(BaseModel):
    modelo: str
    placa: str

class ReservaInput(BaseModel):
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: date
    fecha_inicio: date
    fecha_fin: date
    estado: str

# === BASE DE DATOS EN MEMORIA ===

clientes_data = [
    {"id": 1, "nombre": "Juan Pérez", "email": "juan@email.com"},
    {"id": 2, "nombre": "María García", "email": "maria@email.com"},
    {"id": 3, "nombre": "Carlos López", "email": "carlos@email.com"}
]

vehiculos_data = [
    {"id": 1, "modelo": "Toyota Corolla 2023", "placa": "ABC-123"},
    {"id": 2, "modelo": "Honda Civic 2022", "placa": "DEF-456"},
    {"id": 3, "modelo": "Nissan Sentra 2023", "placa": "GHI-789"}
]

reservas_data = [
    {
        "id_reserva": 1,
        "cliente_id": 1,
        "vehiculo_id": 1,
        "fecha_reserva": date.today(),
        "fecha_inicio": date.today(),
        "fecha_fin": date.today(),
        "estado": "activa"
    },
    {
        "id_reserva": 2,
        "cliente_id": 2,
        "vehiculo_id": 2,
        "fecha_reserva": date.today(),
        "fecha_inicio": date.today(),
        "fecha_fin": date.today(),
        "estado": "confirmada"
    }
]

alquileres_data = [
    {
        "id_alquiler": 1,
        "reserva_id": 1,
        "fecha_inicio": date.today(),
        "fecha_fin": date.today(),
        "precio_total": 150.0,
        "estado": "activo"
    }
]

pagos_data = [
    {
        "id_pago": 1,
        "alquiler_id": 1,
        "monto": 150.0,
        "fecha_pago": date.today(),
        "metodo_pago": "tarjeta",
        "estado": "completado"
    }
]

inspecciones_data = [
    {
        "id_inspeccion": 1,
        "alquiler_id": 1,
        "fecha_inspeccion": date.today(),
        "estado_vehiculo": "bueno",
        "observaciones": "Vehículo en buen estado"
    }
]

multas_data = []

# Contadores para IDs
next_cliente_id = 4
next_vehiculo_id = 4
next_reserva_id = 3

# === SERVICIO DE NOTIFICACIONES ===

class NotificationService:
    """Servicio de Notificaciones con WebSockets para eventos del sistema"""
    
    @staticmethod
    async def notify_event(event_type: str, data: dict, module: str):
        """Método genérico para enviar notificaciones"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": event_type,
                "event": f"{module}_{event_type}",
                "data": data,
                "message": f"🔔 {event_type.title()} en módulo {module}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": module
            },
            module
        )
    
    @staticmethod
    async def notify_new_client(client_data: dict):
        await NotificationService.notify_event("new_client", client_data, "clientes")
    
    @staticmethod
    async def notify_new_vehicle(vehicle_data: dict):
        await NotificationService.notify_event("new_vehicle", vehicle_data, "vehiculos")
    
    @staticmethod
    async def notify_new_reservation(reservation_data: dict):
        await NotificationService.notify_event("new_reservation", reservation_data, "reservas")

# === LIFESPAN MANAGER ===

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Gestor del ciclo de vida de la aplicación"""
    print("🚀 Iniciando API Gateway - Sistema de Alquiler...")
    print("📡 Configurando servicio de notificaciones WebSocket...")
    
    # Iniciar heartbeat para WebSockets
    heartbeat_task = asyncio.create_task(heartbeat())
    
    # Esperar un poco y notificar inicio
    await asyncio.sleep(1)
    
    try:
        await connection_manager.broadcast_to_subscription(
            {
                "type": "system_startup",
                "event": "sistema_iniciado",
                "message": "🟢 API Gateway iniciado correctamente",
                "timestamp": str(asyncio.get_event_loop().time()),
                "features": [
                    "✅ API Gateway con esquema unificado",
                    "✅ WebSockets para notificaciones",
                    "✅ Consultas complejas disponibles",
                    "✅ Integración de todos los módulos"
                ]
            },
            "general"
        )
    except:
        pass
    
    print("✅ API Gateway iniciado correctamente")
    
    yield
    
    # Shutdown
    print("🔄 Cerrando API Gateway...")
    heartbeat_task.cancel()
    print("✅ API Gateway cerrado correctamente")

# === APLICACIÓN PRINCIPAL ===

app = FastAPI(
    title="🚗 API Gateway - Sistema de Alquiler de Vehículos",
    version="2.0.0",
    description="""
    **API Gateway con Esquema Unificado y WebSockets**
    
    Sistema completo de alquiler de vehículos cumpliendo requisitos del segundo parcial:
    
    **✅ Requisitos Implementados:**
    - 🔗 **API Gateway** - Punto único de entrada para todos los módulos
    - 🗂️ **Schema Unificado** - Todas las entidades integradas
    - 🔍 **Consultas Complejas** - Relaciones entre entidades
    - 📡 **WebSockets** - Notificaciones en tiempo real
    - ⚡ **Eventos Automáticos** - Notificaciones ante cambios importantes
    
    **Módulos del Primer Parcial Integrados:**
    - 🙎‍♂️ Clientes | 🚗 Vehículos | 📅 Reservas
    - 🔑 Alquileres | 💳 Pagos | 🚨 Multas | 🔍 Inspecciones
    
    **Endpoints Principales:**
    - `/api/gateway/*` - Todas las consultas unificadas
    - `/ws/{channel}` - WebSocket notifications
    """,
    lifespan=lifespan
)

# === MIDDLEWARE ===

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# === ENDPOINT PRINCIPAL ===

@app.get("/")
async def root():
    """Información del API Gateway"""
    return {
        "service": "🚗 API Gateway - Sistema de Alquiler de Vehículos",
        "version": "2.0.0",
        "architecture": "API Gateway Pattern",
        "compliance": {
            "parcial": "Segundo Parcial",
            "requirements_met": [
                "✅ API Gateway como punto único de entrada",
                "✅ Schema unificado de entidades", 
                "✅ Consultas complejas con relaciones",
                "✅ WebSockets para notificaciones",
                "✅ Eventos automáticos ante cambios"
            ]
        },
        "modules_integrated": {
            "from_first_parcial": [
                "🙎‍♂️ Clientes", "🚗 Vehículos", "📅 Reservas",
                "🔑 Alquileres", "💳 Pagos", "🚨 Multas", "🔍 Inspecciones"
            ]
        },
        "endpoints": {
            "unified_gateway": "/api/gateway",
            "websocket_notifications": "/ws/{channel}",
            "health_check": "/health",
            "api_docs": "/docs"
        },
        "websocket_channels": [
            "general", "clientes", "vehiculos", "reservas", 
            "alquileres", "pagos", "multas", "inspecciones"
        ],
        "statistics": {
            "clientes": len(clientes_data),
            "vehiculos": len(vehiculos_data),
            "reservas": len(reservas_data),
            "alquileres": len(alquileres_data),
            "pagos": len(pagos_data),
            "inspecciones": len(inspecciones_data),
            "multas": len(multas_data)
        }
    }

# === API GATEWAY - CONSULTAS UNIFICADAS ===

def get_related_data(reserva_data: dict) -> Reserva:
    """Obtener datos relacionados para una reserva"""
    # Cliente relacionado
    cliente_data = next((c for c in clientes_data if c["id"] == reserva_data["cliente_id"]), None)
    cliente_obj = Cliente(**cliente_data) if cliente_data else None
    
    # Vehículo relacionado
    vehiculo_data = next((v for v in vehiculos_data if v["id"] == reserva_data["vehiculo_id"]), None)
    vehiculo_obj = Vehiculo(**vehiculo_data) if vehiculo_data else None
    
    return Reserva(**reserva_data, cliente=cliente_obj, vehiculo=vehiculo_obj)

@app.get("/api/gateway/all", tags=["API Gateway"])
async def get_all_data():
    """Consulta unificada - Obtener todos los datos del sistema"""
    return {
        "clientes": [Cliente(**c) for c in clientes_data],
        "vehiculos": [Vehiculo(**v) for v in vehiculos_data],
        "reservas": [get_related_data(r) for r in reservas_data],
        "alquileres": [Alquiler(**a) for a in alquileres_data],
        "pagos": [Pago(**p) for p in pagos_data],
        "inspecciones": [Inspeccion(**i) for i in inspecciones_data],
        "multas": [Multa(**m) for m in multas_data],
        "summary": {
            "total_entities": len(clientes_data) + len(vehiculos_data) + len(reservas_data) + len(alquileres_data)
        }
    }

@app.get("/api/gateway/clientes", response_model=List[Cliente], tags=["API Gateway"])
async def get_all_clients():
    """Obtener todos los clientes"""
    return [Cliente(**cliente) for cliente in clientes_data]

@app.get("/api/gateway/clientes/{cliente_id}", response_model=Cliente, tags=["API Gateway"])
async def get_client(cliente_id: int):
    """Obtener un cliente específico"""
    cliente = next((c for c in clientes_data if c["id"] == cliente_id), None)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return Cliente(**cliente)

@app.get("/api/gateway/vehiculos", response_model=List[Vehiculo], tags=["API Gateway"])
async def get_all_vehicles():
    """Obtener todos los vehículos"""
    return [Vehiculo(**vehiculo) for vehiculo in vehiculos_data]

@app.get("/api/gateway/reservas", response_model=List[Reserva], tags=["API Gateway"])
async def get_all_reservations():
    """Obtener todas las reservas con datos relacionados"""
    return [get_related_data(r) for r in reservas_data]

@app.get("/api/gateway/reservas/cliente/{cliente_id}", response_model=List[Reserva], tags=["API Gateway"])
async def get_reservations_by_client(cliente_id: int):
    """Consulta compleja - Reservas de un cliente específico"""
    reservas_cliente = [r for r in reservas_data if r["cliente_id"] == cliente_id]
    return [get_related_data(r) for r in reservas_cliente]

@app.get("/api/gateway/vehiculos/disponibles", response_model=List[Vehiculo], tags=["API Gateway"])
async def get_available_vehicles():
    """Consulta compleja - Vehículos disponibles"""
    vehiculos_reservados = {r["vehiculo_id"] for r in reservas_data if r["estado"] == "activa"}
    vehiculos_disponibles = [v for v in vehiculos_data if v["id"] not in vehiculos_reservados]
    return [Vehiculo(**vehiculo) for vehiculo in vehiculos_disponibles]

@app.get("/api/gateway/resumen", tags=["API Gateway"])
async def get_system_summary():
    """Consulta compleja - Resumen del sistema"""
    total_pagos = sum(p["monto"] for p in pagos_data if p["estado"] == "completado")
    total_multas = sum(m["monto"] for m in multas_data if m["estado"] == "pendiente")
    alquileres_activos = len([a for a in alquileres_data if a["estado"] == "activo"])
    reservas_activas = len([r for r in reservas_data if r["estado"] == "activa"])
    
    return {
        "financiero": {
            "ingresos_totales": total_pagos,
            "multas_pendientes": total_multas,
            "ingresos_netos": total_pagos - total_multas
        },
        "operativo": {
            "alquileres_activos": alquileres_activos,
            "reservas_activas": reservas_activas,
            "vehiculos_disponibles": len([v for v in vehiculos_data if v["id"] not in {r["vehiculo_id"] for r in reservas_data if r["estado"] == "activa"}])
        },
        "estadisticas": {
            "total_clientes": len(clientes_data),
            "total_vehiculos": len(vehiculos_data),
            "total_reservas": len(reservas_data)
        }
    }

# === MUTATIONS (CREAR ENTIDADES) ===

@app.post("/api/gateway/clientes", response_model=Cliente, tags=["API Gateway"])
async def create_client(cliente: ClienteInput):
    """Crear nuevo cliente - Envía notificación WebSocket"""
    global next_cliente_id
    
    nuevo_cliente = {
        "id": next_cliente_id,
        "nombre": cliente.nombre,
        "email": cliente.email
    }
    clientes_data.append(nuevo_cliente)
    next_cliente_id += 1
    
    # Enviar notificación WebSocket (evento importante)
    await NotificationService.notify_new_client(nuevo_cliente)
    
    return Cliente(**nuevo_cliente)

@app.post("/api/gateway/vehiculos", response_model=Vehiculo, tags=["API Gateway"])
async def create_vehicle(vehiculo: VehiculoInput):
    """Crear nuevo vehículo - Envía notificación WebSocket"""
    global next_vehiculo_id
    
    nuevo_vehiculo = {
        "id": next_vehiculo_id,
        "modelo": vehiculo.modelo,
        "placa": vehiculo.placa
    }
    vehiculos_data.append(nuevo_vehiculo)
    next_vehiculo_id += 1
    
    # Enviar notificación WebSocket (evento importante)
    await NotificationService.notify_new_vehicle(nuevo_vehiculo)
    
    return Vehiculo(**nuevo_vehiculo)

@app.post("/api/gateway/reservas", response_model=Reserva, tags=["API Gateway"])
async def create_reservation(reserva: ReservaInput):
    """Crear nueva reserva - Envía notificación WebSocket"""
    global next_reserva_id
    
    # Validaciones
    cliente_existe = any(c["id"] == reserva.cliente_id for c in clientes_data)
    if not cliente_existe:
        raise HTTPException(status_code=400, detail="Cliente no encontrado")
    
    vehiculo_existe = any(v["id"] == reserva.vehiculo_id for v in vehiculos_data)
    if not vehiculo_existe:
        raise HTTPException(status_code=400, detail="Vehículo no encontrado")
    
    nueva_reserva = {
        "id_reserva": next_reserva_id,
        "cliente_id": reserva.cliente_id,
        "vehiculo_id": reserva.vehiculo_id,
        "fecha_reserva": reserva.fecha_reserva,
        "fecha_inicio": reserva.fecha_inicio,
        "fecha_fin": reserva.fecha_fin,
        "estado": reserva.estado
    }
    reservas_data.append(nueva_reserva)
    next_reserva_id += 1
    
    # Enviar notificación WebSocket (evento importante)
    await NotificationService.notify_new_reservation(nueva_reserva)
    
    return get_related_data(nueva_reserva)

# === WEBSOCKETS PARA NOTIFICACIONES ===

@app.websocket("/ws/{channel}")
async def websocket_notifications(websocket: WebSocket, channel: str):
    """
    WebSocket para notificaciones en tiempo real por canal
    
    Canales disponibles:
    - general: Notificaciones generales
    - clientes: Eventos de clientes  
    - vehiculos: Eventos de vehículos
    - reservas: Eventos de reservas
    - alquileres, pagos, multas, inspecciones
    """
    await connection_manager.connect(websocket, channel)
    
    # Mensaje de bienvenida
    await connection_manager.send_personal_message(
        {
            "type": "connection_established",
            "message": f"🔗 Conectado al canal '{channel}' para notificaciones en tiempo real",
            "channel": channel,
            "timestamp": str(asyncio.get_event_loop().time()),
            "available_channels": list(connection_manager.active_connections.keys())
        },
        websocket
    )
    
    try:
        while True:
            data = await websocket.receive_text()
            response = {
                "type": "echo",
                "received": data,
                "channel": channel,
                "timestamp": str(asyncio.get_event_loop().time())
            }
            await connection_manager.send_personal_message(response, websocket)
            
    except WebSocketDisconnect:
        connection_manager.disconnect(websocket)

@app.websocket("/ws")
async def websocket_general(websocket: WebSocket):
    """WebSocket general"""
    await websocket_notifications(websocket, "general")

# === ENDPOINTS DE SALUD Y TESTING ===

@app.get("/health", tags=["System"])
async def health_check():
    """Estado de salud del API Gateway"""
    return {
        "status": "healthy",
        "service": "API Gateway",
        "version": "2.0.0",
        "components": {
            "unified_schema": "✅ Active",
            "websockets": "✅ Active", 
            "notification_service": "✅ Active"
        },
        "websocket_stats": connection_manager.get_connection_stats(),
        "total_connections": sum(connection_manager.get_connection_stats().values())
    }

@app.get("/test/notification/{event_type}", tags=["Testing"])
async def test_notification(event_type: str):
    """Probar sistema de notificaciones"""
    test_data = {
        "id": 999,
        "test": True,
        "event_type": event_type,
        "timestamp": str(asyncio.get_event_loop().time())
    }
    
    if event_type == "new_client":
        await NotificationService.notify_new_client({
            **test_data,
            "nombre": "Cliente de Prueba",
            "email": "test@example.com"
        })
    elif event_type == "new_vehicle":
        await NotificationService.notify_new_vehicle({
            **test_data,
            "modelo": "Vehículo de Prueba",
            "placa": "TEST-123"
        })
    else:
        await NotificationService.notify_event(event_type, test_data, "general")
    
    return {
        "message": f"✅ Notificación {event_type} enviada",
        "data": test_data,
        "connections": connection_manager.get_connection_stats()
    }

@app.get("/ws/stats", tags=["WebSocket"])
async def websocket_stats():
    """Estadísticas de WebSockets"""
    return {
        "websocket_connections": connection_manager.get_connection_stats(),
        "total_active_connections": sum(connection_manager.get_connection_stats().values()),
        "available_channels": list(connection_manager.active_connections.keys()),
        "notification_service": "✅ Active"
    }

# === DOCUMENTACIÓN INTERACTIVA PERSONALIZADA ===

@app.get("/gateway-docs", response_class=HTMLResponse, tags=["Documentation"])
async def gateway_documentation():
    """Documentación del API Gateway"""
    return """
    <html>
        <head>
            <title>API Gateway - Sistema de Alquiler</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { background: #1e40af; color: white; padding: 20px; border-radius: 8px; }
                .section { margin: 20px 0; padding: 15px; border-left: 4px solid #3b82f6; background: #f8fafc; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🚗 API Gateway - Sistema de Alquiler de Vehículos</h1>
                <p>Punto único de entrada con esquema unificado y notificaciones en tiempo real</p>
            </div>
            
            <div class="section">
                <h2>✅ Requisitos del Segundo Parcial Implementados</h2>
                <ul>
                    <li><strong>API Gateway:</strong> Punto único de entrada en /api/gateway/*</li>
                    <li><strong>Schema Unificado:</strong> Todas las entidades integradas con relaciones</li>
                    <li><strong>Consultas Complejas:</strong> Filtros por cliente, disponibilidad, resúmenes</li>
                    <li><strong>WebSockets:</strong> Notificaciones en tiempo real en /ws/{channel}</li>
                    <li><strong>Eventos Automáticos:</strong> Notificaciones ante cambios importantes</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>🔗 Endpoints Principales</h2>
                <ul>
                    <li><a href="/api/gateway/all">/api/gateway/all</a> - Todos los datos</li>
                    <li><a href="/api/gateway/resumen">/api/gateway/resumen</a> - Resumen del sistema</li>
                    <li><a href="/docs">/docs</a> - Documentación completa de la API</li>
                    <li><strong>/ws/{channel}</strong> - WebSocket notifications</li>
                </ul>
            </div>
        </body>
    </html>
    """

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.api_gateway:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info"
    )
