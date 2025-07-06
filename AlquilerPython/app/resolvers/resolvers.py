import graphene
from graphene import ObjectType, Field, List, Mutation, String, Int, Boolean
from sqlalchemy.orm import Session
from app.database import get_db
from app.services.alquiler_service import ClienteService, VehiculoService, ReservaService, AlquilerService
from app.schemas.types import (
    ClienteType, VehiculoType, ReservaType, AlquilerType,
    ClienteInput, VehiculoInput, ReservaInput, AlquilerInput
)
from app.models import Cliente, Vehiculo, Reserva, Alquiler

class Query(ObjectType):
    clientes = List(ClienteType)
    cliente = Field(ClienteType, id=Int(required=True))
    vehiculos = List(VehiculoType)
    vehiculo = Field(VehiculoType, id=Int(required=True))
    reservas = List(ReservaType)
    reserva = Field(ReservaType, id=Int(required=True))
    alquileres = List(AlquilerType)
    alquiler = Field(AlquilerType, id=Int(required=True))

    def resolve_clientes(self, info):
        """Obtener todos los clientes"""
        db = next(get_db())
        try:
            service = ClienteService(db)
            clientes = service.get_all()
            return clientes
        finally:
            db.close()

    def resolve_cliente(self, info, id):
        """Obtener un cliente por ID"""
        db = next(get_db())
        try:
            service = ClienteService(db)
            return service.get_by_id(id)
        finally:
            db.close()

    def resolve_vehiculos(self, info):
        """Obtener todos los vehículos"""
        db = next(get_db())
        try:
            service = VehiculoService(db)
            return service.get_all()
        finally:
            db.close()

    def resolve_vehiculo(self, info, id):
        """Obtener un vehículo por ID"""
        db = next(get_db())
        try:
            service = VehiculoService(db)
            return service.get_by_id(id)
        finally:
            db.close()

    def resolve_reservas(self, info):
        """Obtener todas las reservas"""
        db = next(get_db())
        try:
            service = ReservaService(db)
            return service.get_all()
        finally:
            db.close()

    def resolve_reserva(self, info, id):
        """Obtener una reserva por ID"""
        db = next(get_db())
        try:
            service = ReservaService(db)
            return service.get_by_id(id)
        finally:
            db.close()

    def resolve_alquileres(self, info):
        """Obtener todos los alquileres"""
        db = next(get_db())
        try:
            service = AlquilerService(db)
            return service.get_all()
        finally:
            db.close()

    def resolve_alquiler(self, info, id):
        """Obtener un alquiler por ID"""
        db = next(get_db())
        try:
            service = AlquilerService(db)
            return service.get_by_id(id)
        finally:
            db.close()

class CrearCliente(Mutation):
    class Arguments:
        cliente_data = ClienteInput(required=True)

    Output = ClienteType

    def mutate(self, info, cliente_data):
        db = next(get_db())
        try:
            service = ClienteService(db)
            cliente = service.create(cliente_data)
            return cliente
        finally:
            db.close()

class ActualizarCliente(Mutation):
    class Arguments:
        id = Int(required=True)
        cliente_data = ClienteInput(required=True)

    Output = ClienteType

    def mutate(self, info, id, cliente_data):
        db = next(get_db())
        try:
            service = ClienteService(db)
            return service.update(id, cliente_data)
        finally:
            db.close()

class EliminarCliente(Mutation):
    class Arguments:
        id = Int(required=True)

    Output = Boolean

    def mutate(self, info, id):
        db = next(get_db())
        try:
            service = ClienteService(db)
            return service.delete(id)
        finally:
            db.close()

class CrearVehiculo(Mutation):
    class Arguments:
        vehiculo_data = VehiculoInput(required=True)

    Output = VehiculoType

    def mutate(self, info, vehiculo_data):
        db = next(get_db())
        try:
            service = VehiculoService(db)
            vehiculo = service.create(vehiculo_data)
            return vehiculo
        finally:
            db.close()

class CrearReserva(Mutation):
    class Arguments:
        reserva_data = ReservaInput(required=True)

    Output = ReservaType

    def mutate(self, info, reserva_data):
        db = next(get_db())
        try:
            service = ReservaService(db)
            reserva = service.create(reserva_data)
            return reserva
        finally:
            db.close()

class CrearAlquiler(Mutation):
    class Arguments:
        alquiler_data = AlquilerInput(required=True)

    Output = AlquilerType

    def mutate(self, info, alquiler_data):
        db = next(get_db())
        try:
            service = AlquilerService(db)
            alquiler = service.create(alquiler_data)
            return alquiler
        finally:
            db.close()

class Mutation(ObjectType):
    crear_cliente = CrearCliente.Field()
    actualizar_cliente = ActualizarCliente.Field()
    eliminar_cliente = EliminarCliente.Field()
    crear_vehiculo = CrearVehiculo.Field()
    crear_reserva = CrearReserva.Field()
    crear_alquiler = CrearAlquiler.Field()

schema = graphene.Schema(query=Query, mutation=Mutation)
