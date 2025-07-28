import graphene
from graphene_sqlalchemy import SQLAlchemyObjectType
from models.alquiler import Alquiler

class AlquilerType(SQLAlchemyObjectType):
    class Meta:
        model = Alquiler
