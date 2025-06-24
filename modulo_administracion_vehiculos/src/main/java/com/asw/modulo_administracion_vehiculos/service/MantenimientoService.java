package com.asw.modulo_administracion_vehiculos.service;

import com.asw.modulo_administracion_vehiculos.model.Mantenimiento;
import com.asw.modulo_administracion_vehiculos.repository.MantenimientoRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class MantenimientoService {
    private final MantenimientoRepository mantenimientoRepository;

    public MantenimientoService(MantenimientoRepository mantenimientoRepository) {
        this.mantenimientoRepository = mantenimientoRepository;
    }

    public List<Mantenimiento> getMantenimientosByVehiculo() {
        return mantenimientoRepository.findAll();
    }

    public Mantenimiento saveMantenimiento(Mantenimiento mantenimiento) {
        return mantenimientoRepository.save(mantenimiento);
    }
}