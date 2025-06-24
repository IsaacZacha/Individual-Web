package com.asw.modulo_administracion_vehiculos.service;

import com.asw.modulo_administracion_vehiculos.model.Seguro;
import com.asw.modulo_administracion_vehiculos.repository.SeguroRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class SeguroService {
    private final SeguroRepository seguroRepository;

    public SeguroService(SeguroRepository seguroRepository) {
        this.seguroRepository = seguroRepository;
    }

    public List<Seguro> getSegurosByVehiculo() {
        return seguroRepository.findAll();
    }

    public Seguro saveSeguro(Seguro seguro) {
        return seguroRepository.save(seguro);
    }
}