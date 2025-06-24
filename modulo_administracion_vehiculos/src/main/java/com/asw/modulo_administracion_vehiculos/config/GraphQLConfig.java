package com.asw.modulo_administracion_vehiculos.config;

import graphql.GraphQL;
import graphql.schema.GraphQLSchema;
import graphql.schema.idl.RuntimeWiring;
import graphql.schema.idl.SchemaGenerator;
import graphql.schema.idl.SchemaParser;
import graphql.schema.idl.TypeDefinitionRegistry;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.core.io.Resource;
import org.springframework.core.io.support.PathMatchingResourcePatternResolver;

import com.asw.modulo_administracion_vehiculos.resolver.MutationResolver;
import com.asw.modulo_administracion_vehiculos.resolver.QueryResolver;
import com.asw.modulo_administracion_vehiculos.resolver.VehiculoResolver;

import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

@Configuration
public class GraphQLConfig {

    @Bean
    public GraphQL graphQL(QueryResolver queryResolver,
                           MutationResolver mutationResolver,
                           VehiculoResolver vehiculoResolver) throws IOException {

        // Cargar archivo de esquema GraphQL
        PathMatchingResourcePatternResolver resolver = new PathMatchingResourcePatternResolver();
        Resource resource = resolver.getResource("classpath:graphql/schema.graphqls");

        SchemaParser schemaParser = new SchemaParser();
        TypeDefinitionRegistry typeRegistry;

        try (Reader reader = new InputStreamReader(resource.getInputStream())) {
            typeRegistry = schemaParser.parse(reader);
        }

        // Configurar los resolvers
        RuntimeWiring runtimeWiring = RuntimeWiring.newRuntimeWiring()
                .type("Query", typeWiring -> typeWiring
                        .dataFetcher("vehiculos", env -> queryResolver.vehiculos())
                        .dataFetcher("vehiculo", env -> queryResolver.vehiculo(null))
                        // .dataFetcher("vehiculosPorEstado", queryResolver.vehiculosPorEstado(null))
                        )
                .type("Mutation", typeWiring -> typeWiring
                        .dataFetcher("crearVehiculo", env -> mutationResolver.crearVehiculo(null))
                        .dataFetcher("actualizarVehiculo", env -> mutationResolver.actualizarVehiculo(null, null))
                        .dataFetcher("cambiarEstadoVehiculo", env -> mutationResolver.cambiarEstadoVehiculo(null, null))
                        .dataFetcher("eliminarVehiculo", env ->mutationResolver.eliminarVehiculo(null)))
                .type("Vehiculo", typeWiring -> typeWiring
                        .dataFetcher("tipo",env -> vehiculoResolver.getTipo(null))
                        .dataFetcher("mantenimientos",env -> vehiculoResolver.getMantenimientos(null))
                        .dataFetcher("seguros",env -> vehiculoResolver.getSeguros(null))
                        .dataFetcher("combustible", env -> vehiculoResolver.getCombustible(null)))
                .build();

        // Generar esquema
        GraphQLSchema graphQLSchema = new SchemaGenerator()
                .makeExecutableSchema(typeRegistry, runtimeWiring);

        return GraphQL.newGraphQL(graphQLSchema).build();
    }
}
