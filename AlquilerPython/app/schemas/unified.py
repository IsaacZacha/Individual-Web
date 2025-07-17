"""
Esquemas unificados para respuestas del API Gateway
"""
from pydantic import BaseModel
from typing import Any, Optional, Dict, List

class UnifiedResponse(BaseModel):
    """Esquema unificado para todas las respuestas del API Gateway"""
    success: bool
    message: str
    data: Optional[Any] = None
    errors: Optional[List[str]] = None
    timestamp: Optional[str] = None

class GraphQLResponse(BaseModel):
    """Esquema para respuestas GraphQL"""
    data: Optional[Dict[str, Any]] = None
    errors: Optional[List[Dict[str, Any]]] = None

class WebSocketMessage(BaseModel):
    """Esquema para mensajes WebSocket"""
    event: str
    timestamp: str
    data: Any
    channel: Optional[str] = None
