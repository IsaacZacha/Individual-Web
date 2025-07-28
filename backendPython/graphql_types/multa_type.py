import graphene
from graphene_sqlalchemy import SQLAlchemyObjectType
from models.multa import Multa

class MultaType(SQLAlchemyObjectType):
    class Meta:
        model = Multa
