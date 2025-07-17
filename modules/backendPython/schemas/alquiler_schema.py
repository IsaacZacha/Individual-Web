# schemas/alquiler_schema.py
from ariadne import QueryType, MutationType
from ariadne.contrib.federation import FederatedObjectType
from models.alquiler import Alquiler
from models import db
from datetime import datetime

type_defs_str = """
    type Alquiler @key(fields: "id_alquiler") {
        id_alquiler: ID!
        reserva_id: Int!
        fecha_entrega: String!
        fecha_devolucion: String
        kilometraje_inicial: Float!
        kilometraje_final: Float
        total: Float
    }

    extend type Query {
        allAlquileres: [Alquiler!]!
        getAlquiler(id: ID!): Alquiler
    }

    extend type Mutation {
        createAlquiler(
            reserva_id: Int!,
            fecha_entrega: String!,
            kilometraje_inicial: Float!
        ): Alquiler!

        updateAlquiler(
            id: ID!,
            fecha_devolucion: String,
            kilometraje_final: Float,
            total: Float
        ): Alquiler!

        deleteAlquiler(id: ID!): Boolean!
    }
"""

query = QueryType()
mutation = MutationType()
alquiler_federation = FederatedObjectType("Alquiler")

@alquiler_federation.reference_resolver
def resolve_alquiler_reference(_, _info, representation):
    return Alquiler.query.get(representation["id_alquiler"])

@query.field("allAlquileres")
def resolve_all_alquileres(*_):
    return Alquiler.query.all()

@query.field("getAlquiler")
def resolve_get_alquiler(*_, id):
    return Alquiler.query.get(id)

@mutation.field("createAlquiler")
def resolve_create_alquiler(*_, reserva_id, fecha_entrega, kilometraje_inicial):
    alquiler = Alquiler(
        reserva_id=reserva_id,
        fecha_entrega=fecha_entrega,
        kilometraje_inicial=kilometraje_inicial
    )
    db.session.add(alquiler)
    db.session.commit()
    return alquiler

@mutation.field("updateAlquiler")
def resolve_update_alquiler(*_, id, fecha_devolucion=None, kilometraje_final=None, total=None):
    alquiler = Alquiler.query.get(id)
    if not alquiler:
        return None
    if fecha_devolucion is not None:
        alquiler.fecha_devolucion = fecha_devolucion
    if kilometraje_final is not None:
        alquiler.kilometraje_final = kilometraje_final
    if total is not None:
        alquiler.total = total
    db.session.commit()
    return alquiler

@mutation.field("deleteAlquiler")
def resolve_delete_alquiler(*_, id):
    alquiler = Alquiler.query.get(id)
    if not alquiler:
        return False
    db.session.delete(alquiler)
    db.session.commit()
    return True

__all__ = [
    "type_defs_str",
    "query",
    "mutation",
    "alquiler_federation"
]