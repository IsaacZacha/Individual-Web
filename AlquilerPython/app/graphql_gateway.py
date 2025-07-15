"""
API Gateway con GraphQL + WebSockets
Sistema de Alquiler de Vehículos - Segundo Parcial
Punto único de entrada para todos los módulos
"""
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
import asyncio
from datetime import date

# Importar GraphQL con Graphene
from graphene import Schema
from starlette.graphql import GraphQLApp
from app.graphql.schema import schema

# Importar WebSocket manager
from app.websockets.connection_manager import connection_manager, heartbeat

# === WEBSOCKET NOTIFICATIONS SERVICE ===

class NotificationService:
    """Servicio de Notificaciones con WebSockets para eventos del sistema"""
    
    @staticmethod
    async def notify_new_client(client_data: dict):
        """Notificar cuando se crea un nuevo cliente"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "new_client",
                "event": "cliente_creado",
                "data": client_data,
                "message": f"🆕 Nuevo cliente registrado: {client_data.get('nombre')}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "clientes"
            },
            "clientes"
        )
    
    @staticmethod
    async def notify_new_vehicle(vehicle_data: dict):
        """Notificar cuando se agrega un nuevo vehículo"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "new_vehicle", 
                "event": "vehiculo_agregado",
                "data": vehicle_data,
                "message": f"🚗 Nuevo vehículo agregado: {vehicle_data.get('modelo')}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "vehiculos"
            },
            "vehiculos"
        )
    
    @staticmethod
    async def notify_new_reservation(reservation_data: dict):
        """Notificar cuando se crea una nueva reserva"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "new_reservation",
                "event": "reserva_creada", 
                "data": reservation_data,
                "message": f"📅 Nueva reserva creada para cliente {reservation_data.get('cliente_id')}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "reservas"
            },
            "reservas"
        )
    
    @staticmethod
    async def notify_rental_status_change(rental_data: dict, old_status: str, new_status: str):
        """Notificar cambios de estado en alquileres"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "rental_status_change",
                "event": "estado_alquiler_cambiado",
                "data": rental_data,
                "old_status": old_status,
                "new_status": new_status,
                "message": f"🔄 Alquiler {rental_data.get('id_alquiler')} cambió de '{old_status}' a '{new_status}'",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "alquileres"
            },
            "alquileres"
        )
    
    @staticmethod
    async def notify_payment_completed(payment_data: dict):
        """Notificar cuando se completa un pago"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "payment_completed",
                "event": "pago_completado",
                "data": payment_data,
                "message": f"💳 Pago completado: ${payment_data.get('monto')} - {payment_data.get('metodo_pago')}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "pagos"
            },
            "pagos"
        )
    
    @staticmethod
    async def notify_fine_issued(fine_data: dict):
        """Notificar cuando se emite una multa"""
        await connection_manager.broadcast_to_subscription(
            {
                "type": "fine_issued",
                "event": "multa_emitida",
                "data": fine_data,
                "message": f"🚨 Nueva multa emitida: ${fine_data.get('monto')} - {fine_data.get('descripcion')}",
                "timestamp": str(asyncio.get_event_loop().time()),
                "module": "multas"
            },
            "multas"
        )

# === LIFESPAN MANAGER ===

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Gestor del ciclo de vida de la aplicación"""
    # Startup
    print("🚀 Iniciando API Gateway con GraphQL...")
    print("📡 Configurando servicio de notificaciones WebSocket...")
    
    # Iniciar heartbeat para WebSockets
    heartbeat_task = asyncio.create_task(heartbeat())
    
    # Notificación de inicio
    await asyncio.sleep(1)  # Esperar un poco para que se inicialice todo
    
    try:
        await connection_manager.broadcast_to_subscription(
            {
                "type": "system_startup",
                "event": "sistema_iniciado",
                "message": "🟢 Sistema de Alquiler iniciado correctamente",
                "timestamp": str(asyncio.get_event_loop().time()),
                "features": [
                    "✅ API Gateway GraphQL activo",
                    "✅ WebSockets para notificaciones",
                    "✅ Esquema unificado de entidades",
                    "✅ Consultas complejas disponibles"
                ]
            },
            "general"
        )
    except:
        pass  # Si no hay conexiones activas, ignorar
    
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
    **API Gateway con GraphQL y WebSockets**
    
    Sistema completo de alquiler de vehículos con:
    - 🔗 **GraphQL API Gateway** - Punto único de entrada
    - 📡 **WebSockets** - Notificaciones en tiempo real  
    - 🗂️ **Schema Unificado** - Todas las entidades integradas
    - 🔍 **Consultas Complejas** - Relaciones entre entidades
    - ⚡ **Eventos en Tiempo Real** - Notificaciones automáticas
    
    **Módulos Integrados:**
    - Clientes, Vehículos, Reservas
    - Alquileres, Pagos, Multas, Inspecciones
    
    **Endpoints Principales:**
    - `/graphql` - API Gateway GraphQL
    - `/ws/{subscription_type}` - WebSocket notifications
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

# === INTEGRAR GRAPHQL COMO API GATEWAY ===

# Crear aplicación GraphQL
graphql_app = GraphQLApp(schema=schema)

# Montar GraphQL en la aplicación principal
app.mount("/graphql", graphql_app)

# === ENDPOINTS DE INFORMACIÓN ===

@app.get("/")
async def root():
    """Información principal del API Gateway"""
    return {
        "service": "🚗 API Gateway - Sistema de Alquiler de Vehículos",
        "version": "2.0.0",
        "description": "Punto único de entrada con GraphQL y WebSockets",
        "architecture": {
            "pattern": "API Gateway",
            "graphql": "✅ Activo en /graphql",
            "websockets": "✅ Notificaciones en tiempo real", 
            "unified_schema": "✅ Todas las entidades integradas"
        },
        "endpoints": {
            "graphql_gateway": "/graphql",
            "graphql_playground": "/graphql (GraphiQL)",
            "websocket_notifications": "/ws/{subscription_type}",
            "health_check": "/health",
            "api_docs": "/docs"
        },
        "modules_integrated": [
            "🙎‍♂️ Clientes",
            "🚗 Vehículos", 
            "📅 Reservas",
            "🔑 Alquileres",
            "💳 Pagos",
            "🚨 Multas",
            "🔍 Inspecciones"
        ],
        "websocket_channels": [
            "general", "clientes", "vehiculos", "reservas", 
            "alquileres", "pagos", "multas", "inspecciones"
        ],
        "graphql_capabilities": [
            "🔍 Consultas simples y complejas",
            "🔗 Relaciones entre entidades",
            "✏️ Mutaciones para crear datos", 
            "📊 Reportes y estadísticas",
            "🎯 Filtros avanzados"
        ]
    }

@app.get("/health")
async def health_check():
    """Estado de salud del API Gateway"""
    return {
        "status": "healthy",
        "service": "API Gateway GraphQL",
        "version": "2.0.0",
        "components": {
            "graphql": "✅ Active",
            "websockets": "✅ Active",
            "notification_service": "✅ Active"
        },
        "websocket_stats": connection_manager.get_connection_stats(),
        "total_connections": sum(connection_manager.get_connection_stats().values())
    }

# === WEBSOCKETS PARA NOTIFICACIONES ===

@app.websocket("/ws/{subscription_type}")
async def websocket_notifications(websocket: WebSocket, subscription_type: str):
    """
    WebSocket para notificaciones en tiempo real por tipo de suscripción
    
    Canales disponibles:
    - general: Notificaciones generales del sistema
    - clientes: Eventos relacionados con clientes
    - vehiculos: Eventos relacionados con vehículos  
    - reservas: Eventos relacionados con reservas
    - alquileres: Eventos relacionados con alquileres
    - pagos: Eventos relacionados con pagos
    - multas: Eventos relacionados con multas
    - inspecciones: Eventos relacionados con inspecciones
    """
    await connection_manager.connect(websocket, subscription_type)
    
    # Mensaje de bienvenida
    await connection_manager.send_personal_message(
        {
            "type": "connection_established",
            "message": f"🔗 Conectado al canal '{subscription_type}' para notificaciones en tiempo real",
            "subscription": subscription_type,
            "timestamp": str(asyncio.get_event_loop().time()),
            "available_channels": list(connection_manager.active_connections.keys())
        },
        websocket
    )
    
    try:
        while True:
            # Escuchar mensajes del cliente (para interacciones futuras)
            data = await websocket.receive_text()
            
            # Echo del mensaje recibido
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
    """WebSocket para notificaciones generales"""
    await websocket_notifications(websocket, "general")

# === ENDPOINTS DE TESTING ===

@app.get("/test/notification/{event_type}")
async def test_notification(event_type: str):
    """Probar el sistema de notificaciones WebSocket"""
    
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
    elif event_type == "new_reservation":
        await NotificationService.notify_new_reservation({
            **test_data,
            "cliente_id": 999,
            "vehiculo_id": 999
        })
    else:
        # Notificación general
        await connection_manager.broadcast_to_subscription(
            {
                "type": f"test_{event_type}",
                "data": test_data,
                "message": f"🧪 Prueba de notificación: {event_type}",
                "timestamp": str(asyncio.get_event_loop().time())
            },
            "general"
        )
    
    return {
        "message": f"✅ Notificación {event_type} enviada",
        "data": test_data,
        "connections": connection_manager.get_connection_stats()
    }

@app.get("/ws/stats")
async def websocket_stats():
    """Estadísticas de conexiones WebSocket"""
    return {
        "websocket_connections": connection_manager.get_connection_stats(),
        "total_active_connections": sum(connection_manager.get_connection_stats().values()),
        "available_channels": list(connection_manager.active_connections.keys()),
        "notification_service": "✅ Active"
    }

# === EJECUTAR APLICACIÓN ===

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.graphql_gateway:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )
