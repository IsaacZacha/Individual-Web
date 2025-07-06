import graphene
from sqlalchemy.orm import Session
from app.database import SessionLocal, get_db
from app.services.alquiler_service import ClienteService, VehiculoService, ReservaService, AlquilerService
from app.schemas.graphql_types import (
    ClienteType, VehiculoType, ReservaType, AlquilerType,
    ClienteInput, VehiculoInput, ReservaInput, AlquilerInput
)

def get_db_session():
    db = SessionLocal()
    try:
        return db
    finally:
        pass

class Query(graphene.ObjectType):
    clientes = graphene.List(ClienteType)
    cliente = graphene.Field(ClienteType, id=graphene.Int(required=True))
    vehiculos = graphene.List(VehiculoType)
    vehiculo = graphene.Field(VehiculoType, id=graphene.Int(required=True))
    reservas = graphene.List(ReservaType)
    reserva = graphene.Field(ReservaType, id=graphene.Int(required=True))
    alquileres = graphene.List(AlquilerType)
    alquiler = graphene.Field(AlquilerType, id=graphene.Int(required=True))
    
    def resolve_clientes(self, info):
        db = get_db_session()
        try:
            service = ClienteService(db)
            return service.get_all()
        finally:
            db.close()
    
    def resolve_cliente(self, info, id):
        db = get_db_session()
        try:
            service = ClienteService(db)
            return service.get_by_id(id)
        finally:
            db.close()
    
    def resolve_vehiculos(self, info):
        db = get_db_session()
        try:
            service = VehiculoService(db)
            return service.get_all()
        finally:
            db.close()
    
    def resolve_vehiculo(self, info, id):
        db = get_db_session()
        try:
            service = VehiculoService(db)
            return service.get_by_id(id)
        finally:
            db.close()
    
    def resolve_reservas(self, info):
        db = get_db_session()
        try:
            service = ReservaService(db)
            return service.get_all()
        finally:
            db.close()
    
    def resolve_reserva(self, info, id):
        db = get_db_session()
        try:
            service = ReservaService(db)
            return service.get_by_id(id)
        finally:
            db.close()
    
    def resolve_alquileres(self, info):
        db = get_db_session()
        try:
            service = AlquilerService(db)
            return service.get_all()
        finally:
            db.close()
    
    def resolve_alquiler(self, info, id):
        db = get_db_session()
        try:
            service = AlquilerService(db)
            return service.get_by_id(id)
        finally:
            db.close()

class CreateCliente(graphene.Mutation):
    class Arguments:
        cliente_data = ClienteInput(required=True)
    
    cliente = graphene.Field(ClienteType)
    
    def mutate(self, info, cliente_data):
        db = get_db_session()
        try:
            service = ClienteService(db)
            cliente = service.create(cliente_data)
            return CreateCliente(cliente=cliente)
        finally:
            db.close()

class CreateVehiculo(graphene.Mutation):
    class Arguments:
        vehiculo_data = VehiculoInput(required=True)
    
    vehiculo = graphene.Field(VehiculoType)
    
    def mutate(self, info, vehiculo_data):
        db = get_db_session()
        try:
            service = VehiculoService(db)
            vehiculo = service.create(vehiculo_data)
            return CreateVehiculo(vehiculo=vehiculo)
        finally:
            db.close()

class CreateReserva(graphene.Mutation):
    class Arguments:
        reserva_data = ReservaInput(required=True)
    
    reserva = graphene.Field(ReservaType)
    
    def mutate(self, info, reserva_data):
        db = get_db_session()
        try:
            service = ReservaService(db)
            reserva = service.create(reserva_data)
            return CreateReserva(reserva=reserva)
        finally:
            db.close()

class CreateAlquiler(graphene.Mutation):
    class Arguments:
        alquiler_data = AlquilerInput(required=True)
    
    alquiler = graphene.Field(AlquilerType)
    
    def mutate(self, info, alquiler_data):
        db = get_db_session()
        try:
            service = AlquilerService(db)
            alquiler = service.create(alquiler_data)
            return CreateAlquiler(alquiler=alquiler)
        finally:
            db.close()

class Mutation(graphene.ObjectType):
    create_cliente = CreateCliente.Field()
    create_vehiculo = CreateVehiculo.Field()
    create_reserva = CreateReserva.Field()
    create_alquiler = CreateAlquiler.Field()

# Esquema principal
schema = graphene.Schema(query=Query, mutation=Mutation)
