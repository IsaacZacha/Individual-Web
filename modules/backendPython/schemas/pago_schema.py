# schemas/pago_schema.py
from ariadne import QueryType, MutationType
from ariadne.contrib.federation import FederatedObjectType
from models.pago import Pago
from models import db
from datetime import datetime

type_defs_str = """
    type Pago @key(fields: "id_pago") {
        id_pago: ID!
        alquiler_id: Int!
        fecha: String!
        monto: Float!
        metodo: String!
    }

    extend type Query {
        allPagos: [Pago!]!
        getPago(id: ID!): Pago
    }

    extend type Mutation {
        createPago(
            alquiler_id: Int!,
            monto: Float!,
            metodo: String!
        ): Pago!

        updatePago(
            id: ID!,
            metodo: String
        ): Pago!

        deletePago(id: ID!): Boolean!
    }
"""

query = QueryType()
mutation = MutationType()
pago_federation = FederatedObjectType("Pago")

@pago_federation.reference_resolver
def resolve_pago_reference(_, _info, representation):
    return Pago.query.get(representation["id_pago"])

@query.field("allPagos")
def resolve_all_pagos(*_):
    return Pago.query.all()

@query.field("getPago")
def resolve_get_pago(*_, id):
    return Pago.query.get(id)

@mutation.field("createPago")
def resolve_create_pago(*_, alquiler_id, monto, metodo):
    pago = Pago(
        alquiler_id=alquiler_id,
        fecha=datetime.utcnow(),
        monto=monto,
        metodo=metodo
    )
    db.session.add(pago)
    db.session.commit()
    return pago

@mutation.field("updatePago")
def resolve_update_pago(*_, id, metodo=None):
    pago = Pago.query.get(id)
    if not pago:
        return None
    if metodo is not None:
        pago.metodo = metodo
    db.session.commit()
    return pago

@mutation.field("deletePago")
def resolve_delete_pago(*_, id):
    pago = Pago.query.get(id)
    if not pago:
        return False
    db.session.delete(pago)
    db.session.commit()
    return True

__all__ = [
    "type_defs_str",
    "query",
    "mutation",
    "pago_federation"
]