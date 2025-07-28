from ariadne import gql, QueryType, MutationType
from ariadne.contrib.federation import make_federated_schema

# Importar todos los esquemas de forma consistente
from schemas.reserva_schema import (
    type_defs_str as reserva_type_defs,
    query as reserva_query,
    mutation as reserva_mutation,
    reserva_federation
)
from schemas.alquiler_schema import (
    type_defs_str as alquiler_type_defs,
    query as alquiler_query,
    mutation as alquiler_mutation,
    alquiler_federation
)
from schemas.pago_schema import (
    type_defs_str as pago_type_defs,
    query as pago_query,
    mutation as pago_mutation,
    pago_federation
)
from schemas.multa_schema import (
    type_defs_str as multa_type_defs,
    query as multa_query,
    mutation as multa_mutation,
    multa_federation
)
from schemas.inspeccion_schema import (
    type_defs_str as inspeccion_type_defs,
    query as inspeccion_query,
    mutation as inspeccion_mutation,
    inspeccion_federation
)

# Definición de tipos base
BASE_TYPEDEFS = gql("""
    type Query {
        _empty: String
    }

    type Mutation {
        _empty: String
    }
""")

# Lista de todos los módulos a incorporar
SCHEMA_MODULES = [
    {
        'type_defs': reserva_type_defs,
        'query': reserva_query,
        'mutation': reserva_mutation,
        'federation': reserva_federation
    },
    {
        'type_defs': alquiler_type_defs,
        'query': alquiler_query,
        'mutation': alquiler_mutation,
        'federation': alquiler_federation
    },
    {
        'type_defs': pago_type_defs,
        'query': pago_query,
        'mutation': pago_mutation,
        'federation': pago_federation
    },
    {
        'type_defs': multa_type_defs,
        'query': multa_query,
        'mutation': multa_mutation,
        'federation': multa_federation
    },
    {
        'type_defs': inspeccion_type_defs,
        'query': inspeccion_query,
        'mutation': inspeccion_mutation,
        'federation': inspeccion_federation
    }
]

def combine_schemas():
    """Combina todos los esquemas en uno federado"""
    
    # Combinar todas las definiciones de tipo
    combined_type_defs = BASE_TYPEDEFS + "".join(
        module['type_defs'] for module in SCHEMA_MODULES
    )
    
    # Crear tipos Query y Mutation combinados
    combined_query = QueryType()
    combined_mutation = MutationType()
    
    # Combinar todos los resolvers
    federation_objects = []
    
    for module in SCHEMA_MODULES:
        # Copiar resolvers de queries
        if module['query'] and hasattr(module['query'], '_resolvers'):
            for field_name, resolver in module['query']._resolvers.items():
                combined_query.set_field(field_name, resolver)
        
        # Copiar resolvers de mutations
        if module['mutation'] and hasattr(module['mutation'], '_resolvers'):
            for field_name, resolver in module['mutation']._resolvers.items():
                combined_mutation.set_field(field_name, resolver)
        
        # Agregar objetos federados
        if module['federation']:
            federation_objects.append(module['federation'])
    
    # Crear el esquema federado
    schema = make_federated_schema(
        gql(combined_type_defs),
        [combined_query, combined_mutation] + federation_objects
    )
    
    return schema

# Crear el esquema combinado
schema = combine_schemas()