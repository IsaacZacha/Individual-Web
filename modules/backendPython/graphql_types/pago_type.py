import graphene
from graphene_sqlalchemy import SQLAlchemyObjectType
from models.pago import Pago

class PagoType(SQLAlchemyObjectType):
    class Meta:
        model = Pago
