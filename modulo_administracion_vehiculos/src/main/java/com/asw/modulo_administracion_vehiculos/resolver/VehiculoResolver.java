package com.asw.modulo_administracion_vehiculos.resolver;

import graphql.kickstart.tools.GraphQLResolver;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.asw.modulo_administracion_vehiculos.model.Combustible;
import com.asw.modulo_administracion_vehiculos.model.Mantenimiento;
import com.asw.modulo_administracion_vehiculos.model.Seguro;
import com.asw.modulo_administracion_vehiculos.model.TipoVehiculo;
import com.asw.modulo_administracion_vehiculos.model.Vehiculo;
import com.asw.modulo_administracion_vehiculos.repository.CombustibleRepository;
import com.asw.modulo_administracion_vehiculos.repository.MantenimientoRepository;
import com.asw.modulo_administracion_vehiculos.repository.SeguroRepository;
import com.asw.modulo_administracion_vehiculos.repository.TipoVehiculoRepository;

import java.util.List;

@Component
public class VehiculoResolver implements GraphQLResolver<Vehiculo> {

    @Autowired
    private TipoVehiculoRepository tipoVehiculoRepository;
    
    @Autowired
    private MantenimientoRepository mantenimientoRepository;
    
    @Autowired
    private SeguroRepository seguroRepository;
    
    @Autowired
    private CombustibleRepository combustibleRepository;

    public TipoVehiculo getTipo(Vehiculo vehiculo) {
        return vehiculo.getTipo();
    }

    public List<Mantenimiento> getMantenimientos(Vehiculo vehiculo) {
        return mantenimientoRepository.findByVehiculoId(vehiculo.getId());
    }

    public List<Seguro> getSeguros(Vehiculo vehiculo) {
        return seguroRepository.findByVehiculoId(vehiculo.getId());
    }

    public Combustible getCombustible(Vehiculo vehiculo) {
        return combustibleRepository.findByVehiculoId(vehiculo.getId());
    }
}