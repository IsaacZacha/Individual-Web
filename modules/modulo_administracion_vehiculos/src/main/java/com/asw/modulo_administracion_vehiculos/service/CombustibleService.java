package com.asw.modulo_administracion_vehiculos.service;
import org.springframework.stereotype.Service;

import com.asw.modulo_administracion_vehiculos.model.entity.Combustible;
import com.asw.modulo_administracion_vehiculos.repository.CombustibleRepository;

import java.util.List;

@Service
public class CombustibleService {
    private final CombustibleRepository combustibleRepository;

    public CombustibleService(CombustibleRepository combustibleRepository) {
        this.combustibleRepository = combustibleRepository;
    }

    public List<Combustible> findAllCombustibles() {
        return combustibleRepository.findAll();
    }

    public Combustible findCombustibleById(Long id) {
        return combustibleRepository.findById(id).orElse(null);
    }

    public Combustible saveCombustible(Combustible combustible) {
        return combustibleRepository.save(combustible);
    }

    public void deleteCombustible(Long id) {
        combustibleRepository.deleteById(id);
    }

    public List<Combustible> findCombustiblesByVehiculoId(Long vehiculoId) {
        return combustibleRepository.findByVehiculoIdVehiculo(vehiculoId);
    }
}