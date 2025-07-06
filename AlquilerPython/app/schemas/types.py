import graphene
from graphene import ObjectType, String, Int, Float, Date, Field, List
from typing import Optional
from datetime import date

class ClienteType(ObjectType):
    id = Int()
    nombre = String()
    email = String()

class VehiculoType(ObjectType):
    id = Int()
    modelo = String()
    placa = String()

class ReservaType(ObjectType):
    id_reserva = Int()
    cliente_id = Int()
    vehiculo_id = Int()
    fecha_reserva = Date()
    fecha_inicio = Date()
    fecha_fin = Date()
    estado = String()
    cliente = Field(lambda: ClienteType)
    vehiculo = Field(lambda: VehiculoType)

class AlquilerType(ObjectType):
    id_alquiler = Int()
    reserva_id = Int()
    fecha_entrega = Date()
    fecha_devolucion = Date()
    kilometraje_inicial = Float()
    kilometraje_final = Float()
    total = Float()
    reserva = Field(lambda: ReservaType)

class PagoType(ObjectType):
    id_pago = Int()
    alquiler_id = Int()
    fecha = Date()
    monto = Float()
    metodo = String()
    alquiler = Field(lambda: AlquilerType)

class MultaType(ObjectType):
    id_multa = Int()
    alquiler_id = Int()
    motivo = String()
    monto = Float()
    fecha = Date()
    alquiler = Field(lambda: AlquilerType)

class InspeccionType(ObjectType):
    id_inspeccion = Int()
    alquiler_id = Int()
    fecha = Date()
    observaciones = String()
    estado_vehiculo = String()
    alquiler = Field(lambda: AlquilerType)

# Input Types para mutations
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
    estado = String(required=True)

class AlquilerInput(graphene.InputObjectType):
    reserva_id = Int(required=True)
    fecha_entrega = Date(required=True)
    fecha_devolucion = Date()
    kilometraje_inicial = Float(required=True)
    kilometraje_final = Float()
    total = Float(required=True)
