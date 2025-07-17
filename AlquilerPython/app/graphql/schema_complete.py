"""
Resolvers y Query/Mutation classes para GraphQL
"""
import graphene
from graphene import ObjectType, String, Int, Float, Date, List, Field, Mutation, Schema
from datetime import date
from typing import Optional

# === TIPOS GRAPHQL ===

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

# === INPUTS PARA MUTATIONS ===

class ClienteInput(graphene.InputObjectType):
    nombre = String(required=True)
    email = String(required=True)

class VehiculoInput(graphene.InputObjectType):
    modelo = String(required=True)
    placa = String(required=True)

class ReservaInput(graphene.InputObjectType):
    cliente_id = Int(required=True)
    vehiculo_id = Int(required=True)
    fecha_reserva = Date(required=True)
    fecha_inicio = Date(required=True)
    fecha_fin = Date(required=True)
    estado = String()

# === DATOS DE EJEMPLO ===

clientes_data = [
    {"id": 1, "nombre": "Juan Pérez", "email": "juan@email.com"},
    {"id": 2, "nombre": "María García", "email": "maria@email.com"}
]

vehiculos_data = [
    {"id": 1, "modelo": "Toyota Corolla 2023", "placa": "ABC-123"},
    {"id": 2, "modelo": "Honda Civic 2022", "placa": "DEF-456"}
]

reservas_data = [
    {
        "id_reserva": 1, "cliente_id": 1, "vehiculo_id": 1,
        "fecha_reserva": date(2024, 1, 15), "fecha_inicio": date(2024, 1, 20), 
        "fecha_fin": date(2024, 1, 25), "estado": "confirmada"
    }
]

# === QUERIES ===

class Query(graphene.ObjectType):
    # Queries individuales
    cliente = Field(Cliente, id=Int(required=True))
    vehiculo = Field(Vehiculo, id=Int(required=True))
    reserva = Field(Reserva, id=Int(required=True))
    
    # Queries de listas
    clientes = List(Cliente)
    vehiculos = List(Vehiculo)
    reservas = List(Reserva)
    
    # Queries complejas con relaciones
    reservas_por_cliente = List(Reserva, cliente_id=Int(required=True))
    vehiculos_disponibles = List(Vehiculo, fecha_inicio=Date(required=True), fecha_fin=Date(required=True))
    
    def resolve_cliente(self, info, id):
        return next((c for c in clientes_data if c["id"] == id), None)
    
    def resolve_vehiculo(self, info, id):
        return next((v for v in vehiculos_data if v["id"] == id), None)
        
    def resolve_reserva(self, info, id):
        reserva = next((r for r in reservas_data if r["id_reserva"] == id), None)
        if reserva:
            # Resolver relaciones
            cliente = next((c for c in clientes_data if c["id"] == reserva["cliente_id"]), None)
            vehiculo = next((v for v in vehiculos_data if v["id"] == reserva["vehiculo_id"]), None)
            reserva["cliente"] = cliente
            reserva["vehiculo"] = vehiculo
        return reserva
    
    def resolve_clientes(self, info):
        return clientes_data
    
    def resolve_vehiculos(self, info):
        return vehiculos_data
        
    def resolve_reservas(self, info):
        # Resolver todas las relaciones
        reservas_con_relaciones = []
        for reserva in reservas_data:
            reserva_copia = reserva.copy()
            cliente = next((c for c in clientes_data if c["id"] == reserva["cliente_id"]), None)
            vehiculo = next((v for v in vehiculos_data if v["id"] == reserva["vehiculo_id"]), None)
            reserva_copia["cliente"] = cliente
            reserva_copia["vehiculo"] = vehiculo
            reservas_con_relaciones.append(reserva_copia)
        return reservas_con_relaciones
    
    def resolve_reservas_por_cliente(self, info, cliente_id):
        return [r for r in reservas_data if r["cliente_id"] == cliente_id]
        
    def resolve_vehiculos_disponibles(self, info, fecha_inicio, fecha_fin):
        # Lógica simple: vehiculos que no tienen reservas en el rango de fechas
        vehiculos_ocupados = []
        for reserva in reservas_data:
            if (reserva["fecha_inicio"] <= fecha_fin and reserva["fecha_fin"] >= fecha_inicio):
                vehiculos_ocupados.append(reserva["vehiculo_id"])
        
        return [v for v in vehiculos_data if v["id"] not in vehiculos_ocupados]

# === MUTATIONS ===

class CrearCliente(graphene.Mutation):
    class Arguments:
        cliente_data = ClienteInput(required=True)
    
    cliente = Field(Cliente)
    success = graphene.Boolean()
    message = String()
    
    def mutate(self, info, cliente_data):
        # Generar nuevo ID
        nuevo_id = max([c["id"] for c in clientes_data], default=0) + 1
        
        nuevo_cliente = {
            "id": nuevo_id,
            "nombre": cliente_data.nombre,
            "email": cliente_data.email
        }
        
        clientes_data.append(nuevo_cliente)
        
        return CrearCliente(
            cliente=nuevo_cliente,
            success=True,
            message="Cliente creado exitosamente"
        )

class CrearReserva(graphene.Mutation):
    class Arguments:
        reserva_data = ReservaInput(required=True)
    
    reserva = Field(Reserva)
    success = graphene.Boolean()
    message = String()
    
    def mutate(self, info, reserva_data):
        # Validar que existan el cliente y vehículo
        cliente_existe = any(c["id"] == reserva_data.cliente_id for c in clientes_data)
        vehiculo_existe = any(v["id"] == reserva_data.vehiculo_id for v in vehiculos_data)
        
        if not cliente_existe:
            return CrearReserva(
                reserva=None,
                success=False,
                message="Cliente no encontrado"
            )
            
        if not vehiculo_existe:
            return CrearReserva(
                reserva=None,
                success=False,
                message="Vehículo no encontrado"
            )
        
        # Generar nuevo ID
        nuevo_id = max([r["id_reserva"] for r in reservas_data], default=0) + 1
        
        nueva_reserva = {
            "id_reserva": nuevo_id,
            "cliente_id": reserva_data.cliente_id,
            "vehiculo_id": reserva_data.vehiculo_id,
            "fecha_reserva": reserva_data.fecha_reserva,
            "fecha_inicio": reserva_data.fecha_inicio,
            "fecha_fin": reserva_data.fecha_fin,
            "estado": reserva_data.estado or "pendiente"
        }
        
        reservas_data.append(nueva_reserva)
        
        # Agregar relaciones para la respuesta
        cliente = next(c for c in clientes_data if c["id"] == reserva_data.cliente_id)
        vehiculo = next(v for v in vehiculos_data if v["id"] == reserva_data.vehiculo_id)
        nueva_reserva["cliente"] = cliente
        nueva_reserva["vehiculo"] = vehiculo
        
        return CrearReserva(
            reserva=nueva_reserva,
            success=True,
            message="Reserva creada exitosamente"
        )

class Mutation(graphene.ObjectType):
    crear_cliente = CrearCliente.Field()
    crear_reserva = CrearReserva.Field()

# === ESQUEMA COMPLETO ===

schema = Schema(query=Query, mutation=Mutation)
