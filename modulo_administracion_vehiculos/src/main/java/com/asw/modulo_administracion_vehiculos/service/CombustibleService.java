package com.asw.modulo_administracion_vehiculos.service;

import com.asw.modulo_administracion_vehiculos.model.Combustible;
import com.asw.modulo_administracion_vehiculos.repository.CombustibleRepository;

import java.util.List;

import org.springframework.stereotype.Service;

@Service
public class CombustibleService {
    private final CombustibleRepository combustibleRepository;

    public CombustibleService(CombustibleRepository combustibleRepository) {
        this.combustibleRepository = combustibleRepository;
    }

    public List<Combustible> getCombustibleByVehiculo() {
        return combustibleRepository.findAll();
    }
}