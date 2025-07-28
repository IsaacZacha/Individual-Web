# schemas/inspeccion_schema.py
from ariadne import QueryType, MutationType
from ariadne.contrib.federation import FederatedObjectType
from models.inspeccion import Inspeccion
from models import db
from datetime import datetime

type_defs_str = """
    type Inspeccion @key(fields: "id_inspeccion") {
        id_inspeccion: ID!
        alquiler_id: Int!
        fecha: String!
        observaciones: String!
        estado_vehiculo: String!
    }

    extend type Query {
        allInspecciones: [Inspeccion!]!
        getInspeccion(id: ID!): Inspeccion
    }

    extend type Mutation {
        createInspeccion(
            alquiler_id: Int!,
            observaciones: String!,
            estado_vehiculo: String!
        ): Inspeccion!

        updateInspeccion(
            id: ID!,
            observaciones: String,
            estado_vehiculo: String
        ): Inspeccion!

        deleteInspeccion(id: ID!): Boolean!
    }
"""

query = QueryType()
mutation = MutationType()
inspeccion_federation = FederatedObjectType("Inspeccion")

@inspeccion_federation.reference_resolver
def resolve_inspeccion_reference(_, _info, representation):
    return Inspeccion.query.get(representation["id_inspeccion"])

@query.field("allInspecciones")
def resolve_all_inspecciones(*_):
    return Inspeccion.query.all()

@query.field("getInspeccion")
def resolve_get_inspeccion(*_, id):
    return Inspeccion.query.get(id)

@mutation.field("createInspeccion")
def resolve_create_inspeccion(*_, alquiler_id, observaciones, estado_vehiculo):
    inspeccion = Inspeccion(
        alquiler_id=alquiler_id,
        fecha=datetime.utcnow(),
        observaciones=observaciones,
        estado_vehiculo=estado_vehiculo
    )
    db.session.add(inspeccion)
    db.session.commit()
    return inspeccion

@mutation.field("updateInspeccion")
def resolve_update_inspeccion(*_, id, observaciones=None, estado_vehiculo=None):
    inspeccion = Inspeccion.query.get(id)
    if not inspeccion:
        return None
    if observaciones is not None:
        inspeccion.observaciones = observaciones
    if estado_vehiculo is not None:
        inspeccion.estado_vehiculo = estado_vehiculo
    db.session.commit()
    return inspeccion

@mutation.field("deleteInspeccion")
def resolve_delete_inspeccion(*_, id):
    inspeccion = Inspeccion.query.get(id)
    if not inspeccion:
        return False
    db.session.delete(inspeccion)
    db.session.commit()
    return True

__all__ = [
    "type_defs_str",
    "query",
    "mutation",
    "inspeccion_federation"
]