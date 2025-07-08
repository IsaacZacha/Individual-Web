package com.asw.modulo_administracion_vehiculos.service;

import com.asw.modulo_administracion_vehiculos.model.dto.TipoVehiculoInput;
import com.asw.modulo_administracion_vehiculos.model.dto.VehiculoInput;
import com.asw.modulo_administracion_vehiculos.model.entity.*;
import com.asw.modulo_administracion_vehiculos.repository.*;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;

@Service
public class VehiculoService {

    private final VehiculoRepository vehiculoRepository;
    private final TipoVehiculoRepository tipoVehiculoRepository;
    private final MantenimientoRepository mantenimientoRepository;
    private final SeguroRepository seguroRepository;
    private final CombustibleRepository combustibleRepository;

    public VehiculoService(VehiculoRepository vehiculoRepository,
                          TipoVehiculoRepository tipoVehiculoRepository,
                          MantenimientoRepository mantenimientoRepository,
                          SeguroRepository seguroRepository,
                          CombustibleRepository combustibleRepository) {
        this.vehiculoRepository = vehiculoRepository;
        this.tipoVehiculoRepository = tipoVehiculoRepository;
        this.mantenimientoRepository = mantenimientoRepository;
        this.seguroRepository = seguroRepository;
        this.combustibleRepository = combustibleRepository;
    }

    public Vehiculo obtenerVehiculoPorId(Long id) {
        return vehiculoRepository.findById(id).orElse(null);
    }

    public List<Vehiculo> obtenerTodosVehiculos() {
        return vehiculoRepository.findAll();
    }

    public TipoVehiculo obtenerTipoVehiculoPorId(Long id) {
        return tipoVehiculoRepository.findById(id).orElse(null);
    }

    public List<TipoVehiculo> obtenerTodosTiposVehiculo() {
        return tipoVehiculoRepository.findAll();
    }

    public List<Mantenimiento> obtenerMantenimientosPorVehiculo(Long vehiculoId) {
        return mantenimientoRepository.findByVehiculoIdVehiculo(vehiculoId);
    }

    public List<Seguro> obtenerSegurosPorVehiculo(Long vehiculoId) {
        return seguroRepository.findByVehiculoIdVehiculo(vehiculoId);
    }

    public List<Combustible> obtenerCombustiblesPorVehiculo(Long vehiculoId) {
        return combustibleRepository.findByVehiculoIdVehiculo(vehiculoId);
    }

    @Transactional
    public Vehiculo crearVehiculo(VehiculoInput input) {
        TipoVehiculo tipo = tipoVehiculoRepository.findById(input.getTipoId())
                .orElseThrow(() -> new RuntimeException("Tipo de vehículo no encontrado"));
        
        Vehiculo vehiculo = new Vehiculo();
        vehiculo.setPlaca(input.getPlaca());
        vehiculo.setMarca(input.getMarca());
        vehiculo.setModelo(input.getModelo());
        vehiculo.setAnio(input.getAnio());
        vehiculo.setTipo(tipo);
        vehiculo.setEstado(input.getEstado());
        
        return vehiculoRepository.save(vehiculo);
    }

    @Transactional
    public Vehiculo actualizarVehiculo(Long id, VehiculoInput input) {
        Vehiculo vehiculo = vehiculoRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Vehículo no encontrado"));
        
        TipoVehiculo tipo = tipoVehiculoRepository.findById(input.getTipoId())
                .orElseThrow(() -> new RuntimeException("Tipo de vehículo no encontrado"));
        
        vehiculo.setPlaca(input.getPlaca());
        vehiculo.setMarca(input.getMarca());
        vehiculo.setModelo(input.getModelo());
        vehiculo.setAnio(input.getAnio());
        vehiculo.setTipo(tipo);
        vehiculo.setEstado(input.getEstado());
        
        return vehiculoRepository.save(vehiculo);
    }

    @Transactional
    public boolean eliminarVehiculo(Long id) {
        if (vehiculoRepository.existsById(id)) {
            vehiculoRepository.deleteById(id);
            return true;
        }
        return false;
    }

    @Transactional
    public TipoVehiculo crearTipoVehiculo(TipoVehiculoInput input) {
        TipoVehiculo tipo = new TipoVehiculo();
        tipo.setDescripcion(input.getDescripcion());
        tipo.setCapacidad(input.getCapacidad());
        tipo.setTransmision(input.getTransmision());
        
        return tipoVehiculoRepository.save(tipo);
    }

    @Transactional
    public TipoVehiculo actualizarTipoVehiculo(Long id, TipoVehiculoInput input) {
        TipoVehiculo tipo = tipoVehiculoRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Tipo de vehículo no encontrado"));
        
        tipo.setDescripcion(input.getDescripcion());
        tipo.setCapacidad(input.getCapacidad());
        tipo.setTransmision(input.getTransmision());
        
        return tipoVehiculoRepository.save(tipo);
    }

    @Transactional
    public boolean eliminarTipoVehiculo(Long id) {
        if (tipoVehiculoRepository.existsById(id)) {
            tipoVehiculoRepository.deleteById(id);
            return true;
        }
        return false;
    }

    @Transactional
    public Mantenimiento crearMantenimiento(Long vehiculoId, LocalDate fechaInicio, LocalDate fechaFin, 
                                          String descripcion, Double costo) {
        Vehiculo vehiculo = vehiculoRepository.findById(vehiculoId)
                .orElseThrow(() -> new RuntimeException("Vehículo no encontrado"));
        
        Mantenimiento mantenimiento = new Mantenimiento();
        mantenimiento.setVehiculo(vehiculo);
        mantenimiento.setFechaInicio(fechaInicio);
        mantenimiento.setFechaFin(fechaFin);
        mantenimiento.setDescripcion(descripcion);
        mantenimiento.setCosto(costo);
        
        return mantenimientoRepository.save(mantenimiento);
    }
}