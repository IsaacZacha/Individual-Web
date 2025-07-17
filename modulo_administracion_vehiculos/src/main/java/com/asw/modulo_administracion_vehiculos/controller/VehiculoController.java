package com.asw.modulo_administracion_vehiculos.controller;
import org.springframework.graphql.data.method.annotation.Argument;
import org.springframework.graphql.data.method.annotation.MutationMapping;
import org.springframework.graphql.data.method.annotation.QueryMapping;
import org.springframework.graphql.data.method.annotation.SchemaMapping;
import org.springframework.stereotype.Controller;

import com.asw.modulo_administracion_vehiculos.model.entity.Vehiculo;
import com.asw.modulo_administracion_vehiculos.service.VehiculoService;

import java.util.List;

@Controller
public class VehiculoController {
    private final VehiculoService vehiculoService;

    public VehiculoController(VehiculoService vehiculoService) {
        this.vehiculoService = vehiculoService;
    }

    @QueryMapping
    public List<Vehiculo> vehiculos() {
        return vehiculoService.findAllVehiculos();
    }

    @QueryMapping
    public Vehiculo vehiculo(@Argument Long id) {
        return vehiculoService.findVehiculoById(id);
    }

    @MutationMapping
    public Vehiculo crearVehiculo(@Argument VehiculoInput vehiculo) {
        Vehiculo nuevoVehiculo = new Vehiculo();
        nuevoVehiculo.setPlaca(vehiculo.placa());
        nuevoVehiculo.setMarca(vehiculo.marca());
        nuevoVehiculo.setModelo(vehiculo.modelo());
        nuevoVehiculo.setAnio(vehiculo.año());
        nuevoVehiculo.setEstado(vehiculo.estado());
        // El tipo se establecerá en el servicio
        return vehiculoService.saveVehiculo(nuevoVehiculo);
    }

    @MutationMapping
    public Vehiculo actualizarVehiculo(@Argument Long id, @Argument VehiculoInput vehiculo) {
        Vehiculo vehiculoExistente = vehiculoService.findVehiculoById(id);
        if (vehiculoExistente != null) {
            vehiculoExistente.setPlaca(vehiculo.placa());
            vehiculoExistente.setMarca(vehiculo.marca());
            vehiculoExistente.setModelo(vehiculo.modelo());
            vehiculoExistente.setAnio(vehiculo.año());
            vehiculoExistente.setEstado(vehiculo.estado());
            return vehiculoService.saveVehiculo(vehiculoExistente);
        }
        return null;
    }

    @MutationMapping
    public Boolean eliminarVehiculo(@Argument Long id) {
        vehiculoService.deleteVehiculo(id);
        return true;
    }

    // @SchemaMapping(typeName = "Vehiculo", field = "mantenimientos")
    // public List<Mantenimiento> mantenimientos(Vehiculo vehiculo) {
    //     return vehiculo.getMantenimientos();
    // }

    // @SchemaMapping(typeName = "Vehiculo", field = "seguros")
    // public List<Seguro> seguros(Vehiculo vehiculo) {
    //     return vehiculo.getSeguros();
    // }

    // @SchemaMapping(typeName = "Vehiculo", field = "combustibles")
    // public List<Combustible> combustibles(Vehiculo vehiculo) {
    //     return vehiculo.getCombustibles();
    // }

    record VehiculoInput(
            String placa,
            String marca,
            String modelo,
            Integer año,
            String estado,
            Long tipoId
    ) {}
}