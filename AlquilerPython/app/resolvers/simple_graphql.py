"""
GraphQL Router con Sandbox - Consultas complejas
Equivalente a Apollo Server con Landing Page en Python
"""
from fastapi import APIRouter, Depends, HTTPException, Request
from fastapi.responses import HTMLResponse
from sqlalchemy.orm import Session
from typing import Dict, List, Any, Optional
from pydantic import BaseModel

from app.database import get_db
from app.services import (
    ClienteService, VehiculoService, ReservaService, 
    AlquilerService, PagoService, MultaService, InspeccionService
)

graphql_router = APIRouter(tags=["GraphQL"])

graphql_router = APIRouter(tags=["GraphQL"])

class GraphQLQuery(BaseModel):
    query: str
    variables: Optional[Dict[str, Any]] = None

class GraphQLResponse(BaseModel):
    data: Optional[Dict[str, Any]] = None
    errors: Optional[List[str]] = None

# GraphQL Playground completo (como Apollo Studio)
@graphql_router.get("/", response_class=HTMLResponse)
async def graphql_playground(request: Request):
    """
    GraphQL Playground completo - Equivalente a Apollo Studio
    """
    return HTMLResponse(content=f"""
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset=utf-8/>
        <meta name="viewport" content="user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui">
        <title>🚗 GraphQL Playground - Sistema Alquiler</title>
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/graphql-playground-react/build/static/css/index.css" />
        <link rel="shortcut icon" href="//cdn.jsdelivr.net/npm/graphql-playground-react/build/favicon.png" />
        <script src="//cdn.jsdelivr.net/npm/graphql-playground-react/build/static/js/middleware.js"></script>
    </head>
    <body>
        <div id="root">
            <style>
                body {{
                    background-color: rgb(23, 42, 58);
                    font-family: Open Sans, sans-serif;
                    height: 90vh;
                }}
                #root {{
                    height: 100%;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }}
                .loading {{
                    font-size: 32px;
                    font-weight: 200;
                    color: rgba(255, 255, 255, .6);
                    margin-left: 20px;
                }}
                img {{
                    width: 78px;
                    height: 78px;
                }}
                .title {{
                    font-weight: 400;
                }}
            </style>
            <img src="//cdn.jsdelivr.net/npm/graphql-playground-react/build/logo.png" alt="">
            <div class="loading"> Loading
                <span class="title">🚗 Sistema Alquiler GraphQL</span>
            </div>
        </div>
        <script>window.addEventListener('load', function (event) {{
            GraphQLPlayground.init(document.getElementById('root'), {{
                endpoint: '/graphql/',
                settings: {{
                    'editor.theme': 'dark',
                    'editor.cursorShape': 'line',
                    'editor.reuseHeaders': true,
                    'tracing.hideTracingResponse': true,
                    'queryPlan.hideQueryPlanResponse': true,
                    'editor.fontSize': 14,
                    'editor.fontFamily': '"Source Code Pro", "Consolas", "Inconsolata", "Droid Sans Mono", "Monaco", monospace',
                    'request.credentials': 'omit',
                }},
                tabs: [{{
                    endpoint: '/graphql/',
                    query: `# 🚗 Sistema de Alquiler de Vehículos
# Bienvenido al GraphQL Playground

query GetVehiculos {{
  vehiculos {{
    id
    marca
    modelo
    año
    disponible
    precio_por_dia
  }}
}}

query GetClientes {{
  clientes {{
    id
    nombre
    email
    telefono
  }}
}}

query GetReservas {{
  reservas {{
    id
    cliente_id
    vehiculo_id
    fecha_inicio
    fecha_fin
    estado
    precio_total
  }}
}}

# Consultas con relaciones
query GetReservasCompletas {{
  reservas_completas {{
    reserva_id
    cliente_nombre
    vehiculo_info
    fechas
    estado
    precio
  }}
}}
`,
                }}]
            }})
        }})</script>
    </body>
    </html>
    """, media_type="text/html")

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
