import graphene
from graphene import ObjectType, String, Int, Float, Date, List, Field
from graphene_sqlalchemy import SQLAlchemyObjectType
from app.models import Cliente as ClienteModel, Vehiculo as VehiculoModel, Reserva as ReservaModel, Alquiler as AlquilerModel

# Tipos GraphQL basados en los modelos SQLAlchemy
class ClienteType(SQLAlchemyObjectType):
    class Meta:
        model = ClienteModel
        load_instance = True

class VehiculoType(SQLAlchemyObjectType):
    class Meta:
        model = VehiculoModel
        load_instance = True

class ReservaType(SQLAlchemyObjectType):
    class Meta:
        model = ReservaModel
        load_instance = True

class AlquilerType(SQLAlchemyObjectType):
    class Meta:
        model = AlquilerModel
        load_instance = True

# Input Types para mutations
class ClienteInput(graphene.InputObjectType):
    nombre = graphene.String(required=True)
    email = graphene.String(required=True)

class VehiculoInput(graphene.InputObjectType):
    modelo = graphene.String(required=True)
    placa = graphene.String(required=True)

class ReservaInput(graphene.InputObjectType):
    cliente_id = graphene.Int(required=True)
    vehiculo_id = graphene.Int(required=True)
    fecha_reserva = graphene.Date(required=True)
    fecha_inicio = graphene.Date(required=True)
    fecha_fin = graphene.Date(required=True)
    estado = graphene.String(required=True)

class AlquilerInput(graphene.InputObjectType):
    reserva_id = graphene.Int(required=True)
    fecha_entrega = graphene.Date(required=True)
    fecha_devolucion = graphene.Date()
    kilometraje_inicial = graphene.Float(required=True)
    kilometraje_final = graphene.Float()
    total = graphene.Float(required=True)
