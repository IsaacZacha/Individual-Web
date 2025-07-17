package com.asw.modulo_administracion_vehiculos.controller;


import org.springframework.graphql.data.method.annotation.Argument;
import org.springframework.graphql.data.method.annotation.MutationMapping;
import org.springframework.graphql.data.method.annotation.QueryMapping;
import org.springframework.graphql.data.method.annotation.SchemaMapping;
import org.springframework.stereotype.Controller;

import com.asw.modulo_administracion_vehiculos.model.entity.TipoVehiculo;
import com.asw.modulo_administracion_vehiculos.model.entity.Vehiculo;
import com.asw.modulo_administracion_vehiculos.service.TipoVehiculoService;

import java.util.List;

@Controller
public class TipoVehiculoController {
    private final TipoVehiculoService tipoVehiculoService;

    public TipoVehiculoController(TipoVehiculoService tipoVehiculoService) {
        this.tipoVehiculoService = tipoVehiculoService;
    }

    @QueryMapping
    public List<TipoVehiculo> tiposVehiculo() {
        return tipoVehiculoService.findAllTiposVehiculo();
    }

    @QueryMapping
    public TipoVehiculo tipoVehiculo(@Argument Long id) {
        return tipoVehiculoService.findTipoVehiculoById(id);
    }

    @MutationMapping
    public TipoVehiculo crearTipoVehiculo(@Argument TipoVehiculoInput tipoVehiculo) {
        TipoVehiculo nuevoTipo = new TipoVehiculo();
        nuevoTipo.setDescripcion(tipoVehiculo.descripcion());
        nuevoTipo.setCapacidad(tipoVehiculo.capacidad());
        nuevoTipo.setTransmision(tipoVehiculo.transmision());
        return tipoVehiculoService.saveTipoVehiculo(nuevoTipo);
    }

    @MutationMapping
    public TipoVehiculo actualizarTipoVehiculo(@Argument Long id, @Argument TipoVehiculoInput tipoVehiculo) {
        TipoVehiculo tipoExistente = tipoVehiculoService.findTipoVehiculoById(id);
        if (tipoExistente != null) {
            tipoExistente.setDescripcion(tipoVehiculo.descripcion());
            tipoExistente.setCapacidad(tipoVehiculo.capacidad());
            tipoExistente.setTransmision(tipoVehiculo.transmision());
            return tipoVehiculoService.saveTipoVehiculo(tipoExistente);
        }
        return null;
    }

    @MutationMapping
    public Boolean eliminarTipoVehiculo(@Argument Long id) {
        tipoVehiculoService.deleteTipoVehiculo(id);
        return true;
    }

    @SchemaMapping(typeName = "TipoVehiculo", field = "vehiculos")
    public List<Vehiculo> vehiculos(TipoVehiculo tipoVehiculo) {
        return tipoVehiculo.getVehiculos();
    }

    record TipoVehiculoInput(
            String descripcion,
            String capacidad,
            String transmision
    ) {}
}