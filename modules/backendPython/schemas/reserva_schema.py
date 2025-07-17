# schemas/reserva_schema.py
from ariadne import QueryType, MutationType
from ariadne.contrib.federation import FederatedObjectType
from models.reserva import Reserva
from models import db
from datetime import datetime

type_defs_str = """
    type Reserva @key(fields: "id_reserva") {
        id_reserva: ID!
        cliente_id: Int!
        vehiculo_id: Int!
        fecha_reserva: String!
        fecha_inicio: String!
        fecha_fin: String!
        estado: String!
    }

    extend type Query {
        allReservas: [Reserva!]!
        getReserva(id: ID!): Reserva
    }

    extend type Mutation {
        createReserva(
            cliente_id: Int!,
            vehiculo_id: Int!,
            fecha_inicio: String!,
            fecha_fin: String!,
            estado: String!
        ): Reserva!

        updateReserva(
            id: ID!,
            estado: String
        ): Reserva!

        deleteReserva(id: ID!): Boolean!
    }
"""

query = QueryType()
mutation = MutationType()
reserva_federation = FederatedObjectType("Reserva")

@reserva_federation.reference_resolver
def resolve_reserva_reference(_, _info, representation):
    return Reserva.query.get(representation["id_reserva"])

@query.field("allReservas")
def resolve_all_reservas(*_):
    return Reserva.query.all()

@query.field("getReserva")
def resolve_get_reserva(*_, id):
    return Reserva.query.get(id)

@mutation.field("createReserva")
def resolve_create_reserva(*_, cliente_id, vehiculo_id, fecha_inicio, fecha_fin, estado):
    reserva = Reserva(
        cliente_id=cliente_id,
        vehiculo_id=vehiculo_id,
        fecha_reserva=datetime.utcnow(),
        fecha_inicio=fecha_inicio,
        fecha_fin=fecha_fin,
        estado=estado
    )
    db.session.add(reserva)
    db.session.commit()
    return reserva

@mutation.field("updateReserva")
def resolve_update_reserva(*_, id, estado=None):
    reserva = Reserva.query.get(id)
    if not reserva:
        return None
    if estado is not None:
        reserva.estado = estado
    db.session.commit()
    return reserva

@mutation.field("deleteReserva")
def resolve_delete_reserva(*_, id):
    reserva = Reserva.query.get(id)
    if not reserva:
        return False
    db.session.delete(reserva)
    db.session.commit()
    return True

__all__ = [
    "type_defs_str",
    "query",
    "mutation",
    "reserva_federation"
]