package com.asw.modulo_administracion_vehiculos.service;

import org.springframework.stereotype.Service;

import com.asw.modulo_administracion_vehiculos.model.entity.Vehiculo;
import com.asw.modulo_administracion_vehiculos.repository.VehiculoRepository;

import java.util.List;

@Service
public class VehiculoService {
    private final VehiculoRepository vehiculoRepository;

    public VehiculoService(VehiculoRepository vehiculoRepository) {
        this.vehiculoRepository = vehiculoRepository;
    }

    public List<Vehiculo> findAllVehiculos() {
        return vehiculoRepository.findAll();
    }

    public Vehiculo findVehiculoById(Long id) {
        return vehiculoRepository.findById(id).orElse(null);
    }

    public Vehiculo saveVehiculo(Vehiculo vehiculo) {
        return vehiculoRepository.save(vehiculo);
    }

    public void deleteVehiculo(Long id) {
        vehiculoRepository.deleteById(id);
    }
}