package com.asw.modulo_administracion_vehiculos.resolver;



import graphql.kickstart.tools.GraphQLMutationResolver;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.asw.modulo_administracion_vehiculos.dto.VehiculoDTO;
import com.asw.modulo_administracion_vehiculos.model.Vehiculo;
import com.asw.modulo_administracion_vehiculos.service.VehiculoService;

@Component
public class MutationResolver implements GraphQLMutationResolver {

    @Autowired
    private VehiculoService vehiculoService;

    public Vehiculo crearVehiculo(VehiculoDTO input) {
        return vehiculoService.createVehiculo(input);
    }

    public Vehiculo actualizarVehiculo(Long id, VehiculoDTO input) {
        return vehiculoService.updateVehiculo(id, input);
    }

    public Vehiculo cambiarEstadoVehiculo(Long id, String estado) {
        VehiculoDTO dto = new VehiculoDTO();
        dto.setEstado(estado);
        return vehiculoService.updateVehiculo(id, dto);
    }

    public Boolean eliminarVehiculo(Long id) {
        return vehiculoService.deleteVehiculo(id);
    }
}