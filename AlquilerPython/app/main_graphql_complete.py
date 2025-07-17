"""
API Gateway Completo con GraphQL y WebSockets
Sistema de Alquiler de Vehículos - Segundo Parcial
"""
from fastapi import FastAPI, WebSocket, WebSocketDisconnect, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from graphene import Schema
from starlette_graphene3 import GraphQLApp, make_graphiql_handler
from app.graphql.schema import Query, Mutation
from app.websockets.connection_manager import ConnectionManager
from app.schemas.unified import UnifiedResponse
import json
from datetime import date, datetime
from typing import Dict, Any
import asyncio

# Crear esquema GraphQL
schema = Schema(query=Query, mutation=Mutation)

# Crear aplicación FastAPI
app = FastAPI(
    title="🚗 Sistema Alquiler - API Gateway",
    description="API Gateway con GraphQL, REST y WebSockets para el sistema de alquiler de vehículos",
    version="2.0.0"
)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Manager de conexiones WebSocket
manager = ConnectionManager()

# Datos en memoria (simulando microservicios)
clientes_db = [
    {"id": 1, "nombre": "Juan Pérez", "email": "juan@email.com"},
    {"id": 2, "nombre": "María García", "email": "maria@email.com"}
]

vehiculos_db = [
    {"id": 1, "modelo": "Toyota Corolla 2023", "placa": "ABC-123"},
    {"id": 2, "modelo": "Honda Civic 2022", "placa": "DEF-456"}
]

reservas_db = [
    {
        "id_reserva": 1, "cliente_id": 1, "vehiculo_id": 1,
        "fecha_reserva": "2024-01-15", "fecha_inicio": "2024-01-20", 
        "fecha_fin": "2024-01-25", "estado": "confirmada"
    }
]

alquileres_db = [
    {
        "id_alquiler": 1, "reserva_id": 1, "fecha_inicio": "2024-01-20",
        "fecha_fin": "2024-01-25", "precio_total": 250.00, "estado": "activo"
    }
]

pagos_db = [
    {
        "id_pago": 1, "alquiler_id": 1, "monto": 250.00,
        "fecha_pago": "2024-01-20", "metodo_pago": "tarjeta", "estado": "completado"
    }
]

# === FUNCIONES AUXILIARES ===

def serialize_date(obj):
    """Serializar fechas para JSON"""
    if isinstance(obj, (date, datetime)):
        return obj.isoformat()
    return obj

async def notify_clients(channel: str, event_type: str, data: Dict[Any, Any]):
    """Enviar notificación a clientes WebSocket"""
    message = {
        "event": event_type,
        "timestamp": datetime.now().isoformat(),
        "data": json.loads(json.dumps(data, default=serialize_date))
    }
    await manager.broadcast_to_channel(json.dumps(message), channel)

# === GRAPHQL ENDPOINT ===

app.add_route(
    "/graphql", 
    GraphQLApp(schema=schema)
)

app.add_route(
    "/graphiql", 
    make_graphiql_handler()
)

# === WEBSOCKET ENDPOINTS ===

@app.websocket("/ws/{channel}")
async def websocket_endpoint(websocket: WebSocket, channel: str):
    """
    WebSocket endpoint para notificaciones en tiempo real
    Canales disponibles: reservas, alquileres, pagos, general
    """
    await manager.connect(websocket, channel)
    
    # Mensaje de bienvenida
    welcome_msg = {
        "event": "connection_established",
        "timestamp": datetime.now().isoformat(),
        "message": f"Conectado al canal: {channel}",
        "available_channels": ["reservas", "alquileres", "pagos", "general"]
    }
    await websocket.send_text(json.dumps(welcome_msg))
    
    try:
        while True:
            # Mantener la conexión activa
            data = await websocket.receive_text()
            
            # Echo del mensaje recibido
            echo_msg = {
                "event": "message_received",
                "timestamp": datetime.now().isoformat(),
                "original_message": data,
                "response": "Mensaje recibido correctamente"
            }
            await websocket.send_text(json.dumps(echo_msg))
            
    except WebSocketDisconnect:
        manager.disconnect(websocket, channel)

# === API REST CON NOTIFICACIONES WEBSOCKET ===

@app.get("/", response_model=UnifiedResponse)
async def root():
    """Endpoint raíz con información del sistema"""
    return UnifiedResponse(
        success=True,
        message="🚗 API Gateway - Sistema de Alquiler de Vehículos",
        data={
            "version": "2.0.0",
            "endpoints": {
                "graphql": "/graphql",
                "graphiql": "/graphiql",
                "docs": "/docs",
                "websocket": "/ws/{channel}"
            },
            "features": [
                "GraphQL API Gateway",
                "WebSocket Real-time Notifications", 
                "REST APIs",
                "Unified Schema"
            ]
        }
    )

@app.post("/api/clientes", response_model=UnifiedResponse)
async def crear_cliente(cliente: dict):
    """Crear nuevo cliente con notificación WebSocket"""
    nuevo_id = max([c["id"] for c in clientes_db], default=0) + 1
    nuevo_cliente = {
        "id": nuevo_id,
        "nombre": cliente["nombre"],
        "email": cliente["email"]
    }
    clientes_db.append(nuevo_cliente)
    
    # Notificar via WebSocket
    await notify_clients("general", "cliente_creado", nuevo_cliente)
    
    return UnifiedResponse(
        success=True,
        message="Cliente creado exitosamente",
        data=nuevo_cliente
    )

@app.post("/api/reservas", response_model=UnifiedResponse)
async def crear_reserva(reserva: dict):
    """Crear nueva reserva con notificación WebSocket"""
    nuevo_id = max([r["id_reserva"] for r in reservas_db], default=0) + 1
    nueva_reserva = {
        "id_reserva": nuevo_id,
        "cliente_id": reserva["cliente_id"],
        "vehiculo_id": reserva["vehiculo_id"],
        "fecha_reserva": reserva["fecha_reserva"],
        "fecha_inicio": reserva["fecha_inicio"],
        "fecha_fin": reserva["fecha_fin"],
        "estado": reserva.get("estado", "pendiente")
    }
    reservas_db.append(nueva_reserva)
    
    # Notificar via WebSocket
    await notify_clients("reservas", "reserva_creada", nueva_reserva)
    await notify_clients("general", "nueva_actividad", {
        "tipo": "reserva",
        "accion": "creada",
        "id": nuevo_id
    })
    
    return UnifiedResponse(
        success=True,
        message="Reserva creada exitosamente",
        data=nueva_reserva
    )

@app.get("/api/gateway/stats", response_model=UnifiedResponse)
async def obtener_estadisticas():
    """Obtener estadísticas completas del sistema"""
    stats = {
        "total_clientes": len(clientes_db),
        "total_vehiculos": len(vehiculos_db),
        "total_reservas": len(reservas_db),
        "total_alquileres": len(alquileres_db),
        "total_pagos": len(pagos_db),
        "conexiones_websocket": {
            "activas": manager.get_total_connections(),
            "por_canal": manager.get_connections_by_channel()
        },
        "ultima_actualizacion": datetime.now().isoformat()
    }
    
    return UnifiedResponse(
        success=True,
        message="Estadísticas del sistema",
        data=stats
    )

@app.get("/api/gateway/health", response_model=UnifiedResponse)
async def health_check():
    """Health check del API Gateway"""
    return UnifiedResponse(
        success=True,
        message="API Gateway funcionando correctamente",
        data={
            "status": "healthy",
            "timestamp": datetime.now().isoformat(),
            "services": {
                "graphql": "active",
                "websockets": "active", 
                "rest_api": "active"
            }
        }
    )

# === EVENTOS AL INICIO ===

@app.on_event("startup")
async def startup_event():
    """Evento de inicio de la aplicación"""
    print("🚗 API Gateway iniciado exitosamente")
    print("📊 GraphQL endpoint: http://localhost:8000/graphql")
    print("🔍 GraphiQL interface: http://localhost:8000/graphiql")
    print("📡 WebSocket: ws://localhost:8000/ws/{channel}")
    print("📚 Documentación: http://localhost:8000/docs")
    
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
