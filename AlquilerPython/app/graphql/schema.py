"""
API Gateway con GraphQL usando Graphene - Punto único de entrada
Sistema de Alquiler de Vehículos
"""
import graphene
from graphene import ObjectType, String, Int, Float, Date, List, Field, Mutation, Schema
from datetime import date
from typing import Optional

# === TIPOS GRAPHQL CON GRAPHENE ===

class Cliente(graphene.ObjectType):
    id = Int()
    nombre = String()
    email = String()

class Vehiculo(graphene.ObjectType):
    id = Int()
    modelo = String()
    placa = String()

class Reserva(graphene.ObjectType):
    id_reserva = Int()
    cliente_id = Int()
    vehiculo_id = Int()
    fecha_reserva = Date()
    fecha_inicio = Date()
    fecha_fin = Date()
    estado = String()
    # Campos relacionados
    cliente = Field(lambda: Cliente)
    vehiculo = Field(lambda: Vehiculo)

class Alquiler(graphene.ObjectType):
    id_alquiler = Int()
    reserva_id = Int()
    fecha_inicio = Date()
    fecha_fin = Date()
    precio_total = Float()
    estado = String()
    reserva = Field(lambda: Reserva)

class Pago(graphene.ObjectType):
    id_pago = Int()
    alquiler_id = Int()
    monto = Float()
    fecha_pago = Date()
    metodo_pago = String()
    estado = String()
    alquiler = Field(lambda: Alquiler)

class Inspeccion(graphene.ObjectType):
    id_inspeccion = Int()
    alquiler_id = Int()
    fecha_inspeccion = Date()
    estado_vehiculo = String()
    observaciones = String()
    alquiler = Field(lambda: Alquiler)

class Multa(graphene.ObjectType):
    id_multa = Int()
    alquiler_id = Int()
    monto = Float()
    fecha_multa = Date()
    descripcion = String()
    estado = String()
    alquiler = Field(lambda: Alquiler)

# === DATA STORE (Demo) ===

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

next_cliente_id = 4
next_vehiculo_id = 4
next_reserva_id = 3

# === QUERIES ===

class Query(graphene.ObjectType):
    
    # Consultas simples
    clientes = List(Cliente, description="Obtener todos los clientes")
    cliente = Field(Cliente, id=Int(required=True), description="Obtener un cliente por ID")
    vehiculos = List(Vehiculo, description="Obtener todos los vehículos")
    vehiculo = Field(Vehiculo, id=Int(required=True), description="Obtener un vehículo por ID")
    reservas = List(Reserva, description="Obtener todas las reservas")
    reserva = Field(Reserva, id=Int(required=True), description="Obtener una reserva por ID")
    alquileres = List(Alquiler, description="Obtener todos los alquileres")
    pagos = List(Pago, description="Obtener todos los pagos")
    inspecciones = List(Inspeccion, description="Obtener todas las inspecciones")
    multas = List(Multa, description="Obtener todas las multas")
    
    # Consultas complejas
    reservas_por_cliente = List(Reserva, cliente_id=Int(required=True), description="Reservas de un cliente")
    vehiculos_disponibles = List(Vehiculo, description="Vehículos disponibles")
    resumen_financiero = String(description="Resumen financiero del sistema")
    
    def resolve_clientes(self, info):
        return [Cliente(**cliente) for cliente in clientes_data]
    
    def resolve_cliente(self, info, id):
        cliente_data = next((c for c in clientes_data if c["id"] == id), None)
        return Cliente(**cliente_data) if cliente_data else None
    
    def resolve_vehiculos(self, info):
        return [Vehiculo(**vehiculo) for vehiculo in vehiculos_data]
    
    def resolve_vehiculo(self, info, id):
        vehiculo_data = next((v for v in vehiculos_data if v["id"] == id), None)
        return Vehiculo(**vehiculo_data) if vehiculo_data else None
    
    def resolve_reservas(self, info):
        result = []
        for reserva_data in reservas_data:
            # Obtener cliente relacionado
            cliente_data = next((c for c in clientes_data if c["id"] == reserva_data["cliente_id"]), None)
            cliente_obj = Cliente(**cliente_data) if cliente_data else None
            
            # Obtener vehículo relacionado
            vehiculo_data = next((v for v in vehiculos_data if v["id"] == reserva_data["vehiculo_id"]), None)
            vehiculo_obj = Vehiculo(**vehiculo_data) if vehiculo_data else None
            
            reserva = Reserva(**reserva_data)
            reserva.cliente = cliente_obj
            reserva.vehiculo = vehiculo_obj
            result.append(reserva)
        
        return result
    
    def resolve_reserva(self, info, id):
        reserva_data = next((r for r in reservas_data if r["id_reserva"] == id), None)
        if not reserva_data:
            return None
        
        # Obtener datos relacionados
        cliente_data = next((c for c in clientes_data if c["id"] == reserva_data["cliente_id"]), None)
        cliente_obj = Cliente(**cliente_data) if cliente_data else None
        
        vehiculo_data = next((v for v in vehiculos_data if v["id"] == reserva_data["vehiculo_id"]), None)
        vehiculo_obj = Vehiculo(**vehiculo_data) if vehiculo_data else None
        
        reserva = Reserva(**reserva_data)
        reserva.cliente = cliente_obj
        reserva.vehiculo = vehiculo_obj
        return reserva
    
    def resolve_alquileres(self, info):
        return [Alquiler(**alquiler) for alquiler in alquileres_data]
    
    def resolve_pagos(self, info):
        return [Pago(**pago) for pago in pagos_data]
    
    def resolve_inspecciones(self, info):
        return [Inspeccion(**inspeccion) for inspeccion in inspecciones_data]
    
    def resolve_multas(self, info):
        return [Multa(**multa) for multa in multas_data]
    
    def resolve_reservas_por_cliente(self, info, cliente_id):
        reservas_cliente = [r for r in reservas_data if r["cliente_id"] == cliente_id]
        result = []
        
        for reserva_data in reservas_cliente:
            cliente_data = next((c for c in clientes_data if c["id"] == cliente_id), None)
            cliente_obj = Cliente(**cliente_data) if cliente_data else None
            
            vehiculo_data = next((v for v in vehiculos_data if v["id"] == reserva_data["vehiculo_id"]), None)
            vehiculo_obj = Vehiculo(**vehiculo_data) if vehiculo_data else None
            
            reserva = Reserva(**reserva_data)
            reserva.cliente = cliente_obj
            reserva.vehiculo = vehiculo_obj
            result.append(reserva)
        
        return result
    
    def resolve_vehiculos_disponibles(self, info):
        vehiculos_reservados = {r["vehiculo_id"] for r in reservas_data if r["estado"] == "activa"}
        vehiculos_disponibles = [v for v in vehiculos_data if v["id"] not in vehiculos_reservados]
        return [Vehiculo(**vehiculo) for vehiculo in vehiculos_disponibles]
    
    def resolve_resumen_financiero(self, info):
        total_pagos = sum(p["monto"] for p in pagos_data if p["estado"] == "completado")
        total_multas = sum(m["monto"] for m in multas_data if m["estado"] == "pendiente")
        alquileres_activos = len([a for a in alquileres_data if a["estado"] == "activo"])
        
        return f"💰 Ingresos: ${total_pagos:.2f} | 🚨 Multas pendientes: ${total_multas:.2f} | 🚗 Alquileres activos: {alquileres_activos}"

# === MUTATIONS ===

class CrearCliente(graphene.Mutation):
    class Arguments:
        nombre = String(required=True)
        email = String(required=True)
    
    cliente = Field(Cliente)
    success = graphene.Boolean()
    message = String()
    
    def mutate(self, info, nombre, email):
        global next_cliente_id
        nuevo_cliente = {
            "id": next_cliente_id,
            "nombre": nombre,
            "email": email
        }
        clientes_data.append(nuevo_cliente)
        next_cliente_id += 1
        
        return CrearCliente(
            cliente=Cliente(**nuevo_cliente),
            success=True,
            message=f"Cliente {nombre} creado exitosamente"
        )

class CrearVehiculo(graphene.Mutation):
    class Arguments:
        modelo = String(required=True)
        placa = String(required=True)
    
    vehiculo = Field(Vehiculo)
    success = graphene.Boolean()
    message = String()
    
    def mutate(self, info, modelo, placa):
        global next_vehiculo_id
        nuevo_vehiculo = {
            "id": next_vehiculo_id,
            "modelo": modelo,
            "placa": placa
        }
        vehiculos_data.append(nuevo_vehiculo)
        next_vehiculo_id += 1
        
        return CrearVehiculo(
            vehiculo=Vehiculo(**nuevo_vehiculo),
            success=True,
            message=f"Vehículo {modelo} creado exitosamente"
        )

class CrearReserva(graphene.Mutation):
    class Arguments:
        cliente_id = Int(required=True)
        vehiculo_id = Int(required=True)
        fecha_reserva = Date(required=True)
        fecha_inicio = Date(required=True)
        fecha_fin = Date(required=True)
        estado = String(required=True)
    
    reserva = Field(Reserva)
    success = graphene.Boolean()
    message = String()
    
    def mutate(self, info, cliente_id, vehiculo_id, fecha_reserva, fecha_inicio, fecha_fin, estado):
        global next_reserva_id
        
        # Validaciones
        cliente_existe = any(c["id"] == cliente_id for c in clientes_data)
        if not cliente_existe:
            return CrearReserva(success=False, message="Cliente no encontrado")
        
        vehiculo_existe = any(v["id"] == vehiculo_id for v in vehiculos_data)
        if not vehiculo_existe:
            return CrearReserva(success=False, message="Vehículo no encontrado")
        
        nueva_reserva = {
            "id_reserva": next_reserva_id,
            "cliente_id": cliente_id,
            "vehiculo_id": vehiculo_id,
            "fecha_reserva": fecha_reserva,
            "fecha_inicio": fecha_inicio,
            "fecha_fin": fecha_fin,
            "estado": estado
        }
        reservas_data.append(nueva_reserva)
        next_reserva_id += 1
        
        # Crear objeto con relaciones
        cliente_data = next((c for c in clientes_data if c["id"] == cliente_id), None)
        cliente_obj = Cliente(**cliente_data) if cliente_data else None
        
        vehiculo_data = next((v for v in vehiculos_data if v["id"] == vehiculo_id), None)
        vehiculo_obj = Vehiculo(**vehiculo_data) if vehiculo_data else None
        
        reserva_obj = Reserva(**nueva_reserva)
        reserva_obj.cliente = cliente_obj
        reserva_obj.vehiculo = vehiculo_obj
        
        return CrearReserva(
            reserva=reserva_obj,
            success=True,
            message=f"Reserva creada exitosamente para el cliente {cliente_id}"
        )

class Mutations(graphene.ObjectType):
    crear_cliente = CrearCliente.Field()
    crear_vehiculo = CrearVehiculo.Field()
    crear_reserva = CrearReserva.Field()

# === SCHEMA GRAPHQL ===

schema = Schema(query=Query, mutation=Mutations)
