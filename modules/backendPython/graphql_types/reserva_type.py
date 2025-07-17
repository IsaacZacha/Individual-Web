import graphene
from graphene_sqlalchemy import SQLAlchemyObjectType
from models.reserva import Reserva

class ReservaType(SQLAlchemyObjectType):
    class Meta:
        model = Reserva
