from fastapi import FastAPI, WebSocket, WebSocketDisconnect, Request
from fastapi.middleware.cors import CORSMiddleware
from starlette_graphene3 import GraphQLApp, make_graphiql_handler
import asyncio
from contextlib import asynccontextmanager

from app.config import settings
from app.database import init_db, close_db
from app.resolvers.graphql_resolvers import schema
from app.websockets.connection_manager import connection_manager, heartbeat

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
    description="Sistema de Alquiler de Vehículos con GraphQL y WebSockets",
    lifespan=lifespan
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # En producción, especificar dominios específicos
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# GraphQL App
graphql_app = GraphQLApp(schema=schema)

# Montar GraphQL
app.mount("/graphql", graphql_app)

# GraphiQL interface (para desarrollo)
@app.get("/graphiql")
async def graphiql(request: Request):
    return make_graphiql_handler()(request)

# Endpoint de salud
@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "service": settings.app_name,
        "version": settings.app_version
    }

# Endpoint para obtener información de la API
@app.get("/")
async def root():
    return {
        "message": "Sistema de Alquiler de Vehículos API",
        "version": settings.app_version,
        "graphql_endpoint": "/graphql",
        "graphiql_playground": "/graphiql",
        "websocket_endpoint": "/ws/{subscription_type}",
        "health_check": "/health",
        "docs": "/docs"
    }

# WebSocket endpoint para tiempo real
@app.websocket("/ws/{subscription_type}")
async def websocket_endpoint(websocket: WebSocket, subscription_type: str):
    await connection_manager.connect(websocket, subscription_type)
    try:
        while True:
            # Escuchar mensajes del cliente
            data = await websocket.receive_text()
            
            # Procesar mensajes del cliente si es necesario
            # Por ahora, solo enviamos un echo
            response = {
                "type": "echo",
                "received": data,
                "subscription": subscription_type
            }
            await connection_manager.send_personal_message(response, websocket)
            
    except WebSocketDisconnect:
        connection_manager.disconnect(websocket)
        # Notificar desconexión
        await connection_manager.broadcast_to_subscription(
            {
                "type": "client_disconnected",
                "subscription": subscription_type,
                "connections_remaining": len(connection_manager.active_connections.get(subscription_type, []))
            },
            subscription_type
        )

# WebSocket endpoint general (sin tipo específico)
@app.websocket("/ws")
async def websocket_general(websocket: WebSocket):
    await websocket_endpoint(websocket, "general")

# Endpoint para obtener estadísticas de conexiones WebSocket
@app.get("/ws/stats")
async def websocket_stats():
    return {
        "active_connections": connection_manager.get_connection_stats(),
        "total_connections": sum(connection_manager.get_connection_stats().values())
    }

# Endpoints adicionales para testing y demostración
@app.get("/test/notification/{notification_type}")
async def test_notification(notification_type: str):
    """Endpoint para probar notificaciones WebSocket"""
    from app.websockets.connection_manager import (
        notify_nueva_reserva, notify_estado_alquiler, 
        notify_nuevo_pago, notify_inspeccion_completada
    )
    
    test_data = {
        "id": 999,
        "test": True,
        "message": f"Notificación de prueba: {notification_type}"
    }
    
    if notification_type == "reserva":
        test_data["cliente_id"] = 1
        await notify_nueva_reserva(test_data)
    elif notification_type == "alquiler":
        test_data["id_alquiler"] = 1
        await notify_estado_alquiler(test_data)
    elif notification_type == "pago":
        test_data["monto"] = 250.0
        await notify_nuevo_pago(test_data)
    elif notification_type == "inspeccion":
        test_data["estado_vehiculo"] = "EXCELENTE"
        await notify_inspeccion_completada(test_data)
    else:
        await connection_manager.broadcast_to_all({
            "type": "test_notification",
            "data": test_data
        })
    
    return {"message": f"Notificación {notification_type} enviada", "data": test_data}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.main:app",
        host="0.0.0.0",
        port=8000,
        reload=settings.debug,
        log_level="info"
    )
