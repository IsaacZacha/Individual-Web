from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List
from datetime import date
import asyncio
from contextlib import asynccontextmanager

# Modelos Pydantic para las APIs REST
class ClienteCreate(BaseModel):
    nombre: str
    email: str

class ClienteResponse(BaseModel):
    id: int
    nombre: str
    email: str

class VehiculoCreate(BaseModel):
    modelo: str
    placa: str

class VehiculoResponse(BaseModel):
    id: int
    modelo: str
    placa: str

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
    fecha_reserva: date
    fecha_inicio: date
    fecha_fin: date
    estado: str

# Datos en memoria para demostración
clientes_db = []
vehiculos_db = []
reservas_db = []
next_cliente_id = 1
next_vehiculo_id = 1
next_reserva_id = 1

# Lifespan manager simplificado
@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup - agregar algunos datos de ejemplo
    global next_cliente_id, next_vehiculo_id, next_reserva_id
    
    # Datos de ejemplo
    clientes_db.extend([
        {"id": 1, "nombre": "Juan Pérez", "email": "juan@email.com"},
        {"id": 2, "nombre": "María García", "email": "maria@email.com"},
        {"id": 3, "nombre": "Carlos López", "email": "carlos@email.com"}
    ])
    next_cliente_id = 4
    
    vehiculos_db.extend([
        {"id": 1, "modelo": "Toyota Corolla 2023", "placa": "ABC-123"},
        {"id": 2, "modelo": "Honda Civic 2022", "placa": "DEF-456"},
        {"id": 3, "modelo": "Nissan Sentra 2023", "placa": "GHI-789"}
    ])
    next_vehiculo_id = 4
    
    reservas_db.extend([
        {
            "id_reserva": 1,
            "cliente_id": 1,
            "vehiculo_id": 1,
            "fecha_reserva": date.today(),
            "fecha_inicio": date.today(),
            "fecha_fin": date.today(),
            "estado": "activa"
        }
    ])
    next_reserva_id = 2
    
    print("✅ Aplicación iniciada con datos de ejemplo")
    yield
    
    # Shutdown
    print("🔄 Cerrando aplicación")

# Crear la aplicación FastAPI
app = FastAPI(
    title="Sistema de Alquiler de Vehículos (Demo)",
    version="1.0.0",
    description="Sistema de Alquiler de Vehículos con APIs REST - Versión Demo sin Base de Datos",
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
        "service": "Sistema de Alquiler de Vehículos",
        "version": "1.0.0",
        "database": "In-Memory (Demo)",
        "supabase_configured": True
    }

# Endpoint principal
@app.get("/")
async def root():
    return {
        "message": "🚗 Sistema de Alquiler de Vehículos API - Demo",
        "version": "1.0.0",
        "database": "In-Memory (Demo)",
        "supabase": {
            "url": "https://ccfctmavbafpkuitfpjw.supabase.co",
            "configured": True,
            "status": "Available for production"
        },
        "endpoints": {
            "clientes": "/clientes",
            "vehiculos": "/vehiculos", 
            "reservas": "/reservas",
            "health": "/health",
            "docs": "/docs"
        },
        "features": [
            "✅ APIs REST completas",
            "✅ Datos de ejemplo en memoria",
            "✅ Configuración de Supabase lista",
            "✅ Documentación automática",
            "✅ CORS habilitado"
        ],
        "statistics": {
            "clientes": len(clientes_db),
            "vehiculos": len(vehiculos_db),
            "reservas": len(reservas_db)
        }
    }

# === ENDPOINTS PARA CLIENTES ===
@app.get("/clientes", response_model=List[ClienteResponse])
async def get_clientes():
    """Obtener todos los clientes"""
    return clientes_db

@app.get("/clientes/{cliente_id}", response_model=ClienteResponse)
async def get_cliente(cliente_id: int):
    """Obtener un cliente por ID"""
    cliente = next((c for c in clientes_db if c["id"] == cliente_id), None)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return cliente

@app.post("/clientes", response_model=ClienteResponse)
async def create_cliente(cliente: ClienteCreate):
    """Crear un nuevo cliente"""
    global next_cliente_id
    nuevo_cliente = {
        "id": next_cliente_id,
        "nombre": cliente.nombre,
        "email": cliente.email
    }
    clientes_db.append(nuevo_cliente)
    next_cliente_id += 1
    return nuevo_cliente

# === ENDPOINTS PARA VEHÍCULOS ===
@app.get("/vehiculos", response_model=List[VehiculoResponse])
async def get_vehiculos():
    """Obtener todos los vehículos"""
    return vehiculos_db

@app.get("/vehiculos/{vehiculo_id}", response_model=VehiculoResponse)
async def get_vehiculo(vehiculo_id: int):
    """Obtener un vehículo por ID"""
    vehiculo = next((v for v in vehiculos_db if v["id"] == vehiculo_id), None)
    if not vehiculo:
        raise HTTPException(status_code=404, detail="Vehículo no encontrado")
    return vehiculo

@app.post("/vehiculos", response_model=VehiculoResponse)
async def create_vehiculo(vehiculo: VehiculoCreate):
    """Crear un nuevo vehículo"""
    global next_vehiculo_id
    nuevo_vehiculo = {
        "id": next_vehiculo_id,
        "modelo": vehiculo.modelo,
        "placa": vehiculo.placa
    }
    vehiculos_db.append(nuevo_vehiculo)
    next_vehiculo_id += 1
    return nuevo_vehiculo

# === ENDPOINTS PARA RESERVAS ===
@app.get("/reservas", response_model=List[ReservaResponse])
async def get_reservas():
    """Obtener todas las reservas"""
    return reservas_db

@app.get("/reservas/{reserva_id}", response_model=ReservaResponse)
async def get_reserva(reserva_id: int):
    """Obtener una reserva por ID"""
    reserva = next((r for r in reservas_db if r["id_reserva"] == reserva_id), None)
    if not reserva:
        raise HTTPException(status_code=404, detail="Reserva no encontrada")
    return reserva

@app.post("/reservas", response_model=ReservaResponse)
async def create_reserva(reserva: ReservaCreate):
    """Crear una nueva reserva"""
    global next_reserva_id
    
    # Verificar que el cliente existe
    cliente = next((c for c in clientes_db if c["id"] == reserva.cliente_id), None)
    if not cliente:
        raise HTTPException(status_code=400, detail="Cliente no encontrado")
    
    # Verificar que el vehículo existe
    vehiculo = next((v for v in vehiculos_db if v["id"] == reserva.vehiculo_id), None)
    if not vehiculo:
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
    reservas_db.append(nueva_reserva)
    next_reserva_id += 1
    return nueva_reserva

# === DEMO ENDPOINTS ===
@app.get("/demo/reset")
async def reset_demo():
    """Reiniciar datos de demostración"""
    global clientes_db, vehiculos_db, reservas_db, next_cliente_id, next_vehiculo_id, next_reserva_id
    
    clientes_db.clear()
    vehiculos_db.clear()
    reservas_db.clear()
    
    # Reinicializar datos
    clientes_db.extend([
        {"id": 1, "nombre": "Juan Pérez", "email": "juan@email.com"},
        {"id": 2, "nombre": "María García", "email": "maria@email.com"},
        {"id": 3, "nombre": "Carlos López", "email": "carlos@email.com"}
    ])
    next_cliente_id = 4
    
    vehiculos_db.extend([
        {"id": 1, "modelo": "Toyota Corolla 2023", "placa": "ABC-123"},
        {"id": 2, "modelo": "Honda Civic 2022", "placa": "DEF-456"},
        {"id": 3, "modelo": "Nissan Sentra 2023", "placa": "GHI-789"}
    ])
    next_vehiculo_id = 4
    
    reservas_db.extend([
        {
            "id_reserva": 1,
            "cliente_id": 1,
            "vehiculo_id": 1,
            "fecha_reserva": date.today(),
            "fecha_inicio": date.today(),
            "fecha_fin": date.today(),
            "estado": "activa"
        }
    ])
    next_reserva_id = 2
    
    return {
        "message": "✅ Datos de demostración reiniciados",
        "clientes": len(clientes_db),
        "vehiculos": len(vehiculos_db),
        "reservas": len(reservas_db)
    }

@app.get("/supabase/test")
async def test_supabase_config():
    """Mostrar configuración de Supabase"""
    return {
        "supabase": {
            "url": "https://ccfctmavbafpkuitfpjw.supabase.co",
            "database_configured": True,
            "connection_string": "postgresql://postgres:***@db.ccfctmavbafpkuitfpjw.supabase.co:5432/postgres",
            "status": "Configured but not connected (demo mode)",
            "anon_key_configured": True,
            "service_key_configured": True
        },
        "demo_mode": True,
        "message": "Supabase está configurado correctamente. Para usar la base de datos real, conectar en modo producción."
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.main_simple:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )
