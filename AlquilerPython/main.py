"""
API Gateway Completo - FUNCIONAL MEJORADO
Sistema de Alquiler de Vehículos - Arquitectura Limpia
✅ GraphQL ✅ WebSockets ✅ REST API ✅ DTOs ✅ Controllers ✅ Services
"""
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
from datetime import datetime
import asyncio

# Importar componentes organizados
from app.controllers import (
    cliente_router,
    vehiculo_router,
    reserva_router,
    alquiler_router,
    pago_router,
    multa_router,
    inspeccion_router
)
from app.websockets.connection_manager import ConnectionManager
from app.resolvers.simple_graphql import graphql_router


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Manejo de eventos de inicio y cierre de la aplicación"""
    # Startup
    print("\n" + "="*60)
    print("🚗 API GATEWAY COMPLETO - ARQUITECTURA MEJORADA")
    print("="*60)
    print(f"✅ REST API Completa: http://127.0.0.1:8001/")
    print(f"✅ GraphQL Queries: http://127.0.0.1:8001/graphql/")
    print(f"✅ WebSocket Real-Time: ws://127.0.0.1:8001/ws/{{channel}}")
    print(f"✅ Documentación: http://127.0.0.1:8001/docs")
    print("="*60)
    print("📋 CARACTERÍSTICAS:")
    print("   ✅ DTOs para validación de datos")
    print("   ✅ Controllers organizados por entidad")
    print("   ✅ Services con lógica de negocio")
    print("   ✅ Modelos separados por clase")
    print("   ✅ WebSockets para tiempo real")
    print("   ✅ GraphQL para consultas complejas")
    print("="*60)
    
    yield
    
    # Shutdown
    print("🔴 Cerrando API Gateway...")

# Crear la aplicación FastAPI
app = FastAPI(
    title="🚗 Sistema Alquiler - API Gateway COMPLETO",
    description="API Gateway con arquitectura limpia, GraphQL, REST y WebSockets",
    version="3.0.0",
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

# Inicializar Connection Manager
manager = ConnectionManager()

# === WEBSOCKET ENDPOINTS ===

@app.websocket("/ws/{channel}")
async def websocket_endpoint(websocket: WebSocket, channel: str):
    """WebSocket endpoint para comunicación en tiempo real"""
    await manager.connect(websocket, channel)
    try:
        while True:
            # Recibir mensajes del cliente
            data = await websocket.receive_text()
            message_data = {
                "type": "message",
                "channel": channel,
                "message": data,
                "timestamp": datetime.now().isoformat()
            }
            
            # Broadcast a todos los clientes del canal
            await manager.broadcast_to_subscription(message_data, channel)
            
    except WebSocketDisconnect:
        print(f"Cliente desconectado del canal {channel}")
    except Exception as e:
        print(f"WebSocket error: {e}")
    finally:
        manager.disconnect(websocket)

@app.get("/ws/status")
async def websocket_status():
    """Estado de las conexiones WebSocket"""
    total_connections = sum(len(connections) for connections in manager.active_connections.values())
    connections_by_channel = {channel: len(connections) for channel, connections in manager.active_connections.items()}
    
    return {
        "total_connections": total_connections,
        "connections_by_channel": connections_by_channel
    }

# === INCLUIR ROUTERS ===

# REST API Controllers
app.include_router(cliente_router, prefix="/api")
app.include_router(vehiculo_router, prefix="/api")
app.include_router(reserva_router, prefix="/api")
app.include_router(alquiler_router, prefix="/api")
app.include_router(pago_router, prefix="/api")
app.include_router(multa_router, prefix="/api")
app.include_router(inspeccion_router, prefix="/api")

# GraphQL Router
app.include_router(graphql_router, prefix="/graphql")

# === ENDPOINTS PRINCIPALES ===

@app.get("/")
async def root():
    """Endpoint principal con información del sistema"""
    return {
        "message": "🚗 Sistema de Alquiler de Vehículos - API Gateway Completo",
        "version": "3.0.0",
        "features": [
            "REST API completa",
            "GraphQL para consultas complejas",
            "WebSockets para tiempo real",
            "Arquitectura limpia con DTOs",
            "Controllers y Services organizados"
        ],
        "endpoints": {
            "docs": "/docs",
            "rest_api": "/api/",
            "graphql": "/graphql/",
            "websocket": "ws://localhost:8000/ws/{channel}",
            "websocket_status": "/ws/status"
        },
        "entities": [
            "clientes", "vehiculos", "reservas", 
            "alquileres", "pagos", "multas", "inspecciones"
        ]
    }

@app.get("/health")
async def health_check():
    """Health check del sistema"""
    total_connections = sum(len(connections) for connections in manager.active_connections.values())
    return {
        "status": "healthy",
        "framework": "FastAPI",
        "architecture": "Clean Architecture",
        "database": "Supabase",
        "websocket_connections": total_connections
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
