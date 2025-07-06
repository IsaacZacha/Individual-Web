from fastapi import WebSocket, WebSocketDisconnect
from typing import List, Dict
import json
import asyncio
from datetime import datetime

class ConnectionManager:
    def __init__(self):
        # Almacenar conexiones activas por tipo de suscripción
        self.active_connections: Dict[str, List[WebSocket]] = {
            "reservas": [],
            "alquileres": [],
            "pagos": [],
            "inspecciones": [],
            "general": []
        }
    
    async def connect(self, websocket: WebSocket, subscription_type: str = "general"):
        """Conectar un nuevo cliente WebSocket"""
        await websocket.accept()
        if subscription_type not in self.active_connections:
            subscription_type = "general"
        self.active_connections[subscription_type].append(websocket)
        
        # Enviar mensaje de bienvenida
        await self.send_personal_message({
            "type": "connection",
            "message": f"Conectado al canal: {subscription_type}",
            "timestamp": datetime.now().isoformat()
        }, websocket)
    
    def disconnect(self, websocket: WebSocket):
        """Desconectar un cliente WebSocket"""
        for subscription_type, connections in self.active_connections.items():
            if websocket in connections:
                connections.remove(websocket)
    
    async def send_personal_message(self, data: dict, websocket: WebSocket):
        """Enviar mensaje a un cliente específico"""
        try:
            await websocket.send_text(json.dumps(data))
        except:
            # Conexión cerrada, remover de la lista
            self.disconnect(websocket)
    
    async def broadcast_to_subscription(self, data: dict, subscription_type: str):
        """Enviar mensaje a todos los clientes de un tipo de suscripción"""
        if subscription_type in self.active_connections:
            disconnected = []
            for connection in self.active_connections[subscription_type]:
                try:
                    await connection.send_text(json.dumps(data))
                except:
                    disconnected.append(connection)
            
            # Remover conexiones cerradas
            for connection in disconnected:
                self.active_connections[subscription_type].remove(connection)
    
    async def broadcast_to_all(self, data: dict):
        """Enviar mensaje a todos los clientes conectados"""
        for subscription_type in self.active_connections:
            await self.broadcast_to_subscription(data, subscription_type)
    
    def get_connection_stats(self) -> dict:
        """Obtener estadísticas de conexiones"""
        return {
            subscription_type: len(connections)
            for subscription_type, connections in self.active_connections.items()
        }

# Instancia global del manager
connection_manager = ConnectionManager()

# Funciones para notificar eventos específicos
async def notify_nueva_reserva(reserva_data: dict):
    """Notificar cuando se crea una nueva reserva"""
    notification = {
        "type": "nueva_reserva",
        "data": reserva_data,
        "message": f"Nueva reserva creada para el cliente {reserva_data.get('cliente_id')}",
        "timestamp": datetime.now().isoformat()
    }
    await connection_manager.broadcast_to_subscription(notification, "reservas")
    await connection_manager.broadcast_to_subscription(notification, "general")

async def notify_estado_alquiler(alquiler_data: dict):
    """Notificar cambios en el estado de alquileres"""
    notification = {
        "type": "estado_alquiler",
        "data": alquiler_data,
        "message": f"Actualización de alquiler {alquiler_data.get('id_alquiler')}",
        "timestamp": datetime.now().isoformat()
    }
    await connection_manager.broadcast_to_subscription(notification, "alquileres")
    await connection_manager.broadcast_to_subscription(notification, "general")

async def notify_nuevo_pago(pago_data: dict):
    """Notificar cuando se registra un nuevo pago"""
    notification = {
        "type": "nuevo_pago",
        "data": pago_data,
        "message": f"Nuevo pago registrado: ${pago_data.get('monto')}",
        "timestamp": datetime.now().isoformat()
    }
    await connection_manager.broadcast_to_subscription(notification, "pagos")
    await connection_manager.broadcast_to_subscription(notification, "general")

async def notify_inspeccion_completada(inspeccion_data: dict):
    """Notificar cuando se completa una inspección"""
    notification = {
        "type": "inspeccion_completada",
        "data": inspeccion_data,
        "message": f"Inspección completada - Estado: {inspeccion_data.get('estado_vehiculo')}",
        "timestamp": datetime.now().isoformat()
    }
    await connection_manager.broadcast_to_subscription(notification, "inspecciones")
    await connection_manager.broadcast_to_subscription(notification, "general")

# Heartbeat para mantener conexiones vivas
async def heartbeat():
    """Enviar heartbeat cada 30 segundos para mantener conexiones vivas"""
    while True:
        await asyncio.sleep(30)
        heartbeat_data = {
            "type": "heartbeat",
            "timestamp": datetime.now().isoformat(),
            "connections": connection_manager.get_connection_stats()
        }
        await connection_manager.broadcast_to_all(heartbeat_data)
