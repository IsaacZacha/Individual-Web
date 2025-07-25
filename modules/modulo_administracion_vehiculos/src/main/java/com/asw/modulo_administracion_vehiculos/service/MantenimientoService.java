package com.asw.modulo_administracion_vehiculos.service;

import org.springframework.stereotype.Service;

import com.asw.modulo_administracion_vehiculos.model.entity.Mantenimiento;
import com.asw.modulo_administracion_vehiculos.repository.MantenimientoRepository;

import java.util.List;

@Service
public class MantenimientoService {
    private final MantenimientoRepository mantenimientoRepository;

    public MantenimientoService(MantenimientoRepository mantenimientoRepository) {
        this.mantenimientoRepository = mantenimientoRepository;
    }

    public List<Mantenimiento> findAllMantenimientos() {
        return mantenimientoRepository.findAll();
    }

    public Mantenimiento findMantenimientoById(Long id) {
        return mantenimientoRepository.findById(id).orElse(null);
    }

    public Mantenimiento saveMantenimiento(Mantenimiento mantenimiento) {
        return mantenimientoRepository.save(mantenimiento);
    }

    public void deleteMantenimiento(Long id) {
        mantenimientoRepository.deleteById(id);
    }

    public List<Mantenimiento> findMantenimientosByVehiculoId(Long vehiculoId) {
        return mantenimientoRepository.findByVehiculoIdVehiculo(vehiculoId);
    }
}