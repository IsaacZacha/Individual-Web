import graphene
from graphene_sqlalchemy import SQLAlchemyObjectType
from models.inspeccion import Inspeccion

class InspeccionType(SQLAlchemyObjectType):
    class Meta:
        model = Inspeccion
