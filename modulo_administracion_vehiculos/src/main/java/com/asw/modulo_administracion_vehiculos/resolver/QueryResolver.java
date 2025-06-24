package com.asw.modulo_administracion_vehiculos.resolver;

import graphql.kickstart.tools.GraphQLQueryResolver;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.asw.modulo_administracion_vehiculos.model.Vehiculo;
import com.asw.modulo_administracion_vehiculos.service.VehiculoService;

import java.util.List;

@Component
public class QueryResolver implements GraphQLQueryResolver {

    @Autowired
    private VehiculoService vehiculoService;

    public List<Vehiculo> vehiculos() {
        return vehiculoService.getAllVehiculos();
    }

    public Vehiculo vehiculo(Long id) {
        return vehiculoService.getVehiculoById(id);
    }

    public List<Vehiculo> vehiculosPorEstado(String estado) {
        return vehiculoService.getVehiculosByEstado(estado);
    }
}