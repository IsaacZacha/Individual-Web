# schemas/multa_schema.py
from ariadne import QueryType, MutationType
from ariadne.contrib.federation import FederatedObjectType
from models.multa import Multa
from models import db
from datetime import datetime

type_defs_str = """
    type Multa @key(fields: "id_multa") {
        id_multa: ID!
        alquiler_id: Int!
        motivo: String!
        monto: Float!
        fecha: String!
    }

    extend type Query {
        allMultas: [Multa!]!
        getMulta(id: ID!): Multa
    }

    extend type Mutation {
        createMulta(
            alquiler_id: Int!,
            motivo: String!,
            monto: Float!
        ): Multa!

        updateMulta(
            id: ID!,
            motivo: String,
            monto: Float
        ): Multa!

        deleteMulta(id: ID!): Boolean!
    }
"""

query = QueryType()
mutation = MutationType()
multa_federation = FederatedObjectType("Multa")

@multa_federation.reference_resolver
def resolve_multa_reference(_, _info, representation):
    return Multa.query.get(representation["id_multa"])

@query.field("allMultas")
def resolve_all_multas(*_):
    return Multa.query.all()

@query.field("getMulta")
def resolve_get_multa(*_, id):
    return Multa.query.get(id)

@mutation.field("createMulta")
def resolve_create_multa(*_, alquiler_id, motivo, monto):
    multa = Multa(
        alquiler_id=alquiler_id,
        motivo=motivo,
        monto=monto,
        fecha=datetime.utcnow()
    )
    db.session.add(multa)
    db.session.commit()
    return multa

@mutation.field("updateMulta")
def resolve_update_multa(*_, id, motivo=None, monto=None):
    multa = Multa.query.get(id)
    if not multa:
        return None
    if motivo is not None:
        multa.motivo = motivo
    if monto is not None:
        multa.monto = monto
    db.session.commit()
    return multa

@mutation.field("deleteMulta")
def resolve_delete_multa(*_, id):
    multa = Multa.query.get(id)
    if not multa:
        return False
    db.session.delete(multa)
    db.session.commit()
    return True

__all__ = [
    "type_defs_str",
    "query",
    "mutation",
    "multa_federation"
]