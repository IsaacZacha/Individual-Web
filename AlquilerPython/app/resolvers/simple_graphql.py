"""
GraphQL Router Simple - Consultas complejas
"""
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import Dict, List, Any, Optional
from pydantic import BaseModel

from app.database import get_db
from app.services import (
    ClienteService, VehiculoService, ReservaService, 
    AlquilerService, PagoService, MultaService, InspeccionService
)

graphql_router = APIRouter(tags=["GraphQL"])

class GraphQLQuery(BaseModel):
    query: str
    variables: Optional[Dict[str, Any]] = None

class GraphQLResponse(BaseModel):
    data: Optional[Dict[str, Any]] = None
    errors: Optional[List[str]] = None

@graphql_router.post("/", response_model=GraphQLResponse)
async def graphql_query(query_data: GraphQLQuery, db: Session = Depends(get_db)):
    """Endpoint tipo GraphQL para consultas complejas"""
    try:
        query = query_data.query.strip().lower()
        
        # Services
        cliente_service = ClienteService(db)
        vehiculo_service = VehiculoService(db)
        
        if query == "clientes":
            clientes = cliente_service.get_all()
            return GraphQLResponse(data={
                "clientes": [
                    {"id": c.id, "nombre": c.nombre, "email": c.email} 
                    for c in clientes
                ]
            })
            
        elif query == "vehiculos":
            vehiculos = vehiculo_service.get_all()
            return GraphQLResponse(data={
                "vehiculos": [
                    {"id": v.id, "modelo": v.modelo, "placa": v.placa}
                    for v in vehiculos
                ]
            })
            
        elif query == "schema":
            return GraphQLResponse(data={
                "schema": {
                    "queries": ["clientes", "vehiculos", "schema"]
                }
            })
        
        else:
            return GraphQLResponse(errors=[f"Query no reconocida: {query}"])
            
    except Exception as e:
        return GraphQLResponse(errors=[f"Error: {str(e)}"])

@graphql_router.get("/schema")
async def get_schema():
    """Obtener el esquema GraphQL disponible"""
    return {
        "queries": ["clientes", "vehiculos", "schema"],
        "description": "Envía POST con { \"query\": \"nombre_query\" }"
    }
