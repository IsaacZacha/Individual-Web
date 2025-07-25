package com.asw.modulo_administracion_vehiculos.service;
import org.springframework.stereotype.Service;

import com.asw.modulo_administracion_vehiculos.model.entity.Seguro;
import com.asw.modulo_administracion_vehiculos.repository.SeguroRepository;

import java.util.List;

@Service
public class SeguroService {
    private final SeguroRepository seguroRepository;

    public SeguroService(SeguroRepository seguroRepository) {
        this.seguroRepository = seguroRepository;
    }

    public List<Seguro> findAllSeguros() {
        return seguroRepository.findAll();
    }

    public Seguro findSeguroById(Long id) {
        return seguroRepository.findById(id).orElse(null);
    }

    public Seguro saveSeguro(Seguro seguro) {
        return seguroRepository.save(seguro);
    }

    public void deleteSeguro(Long id) {
        seguroRepository.deleteById(id);
    }

    public List<Seguro> findSegurosByVehiculoId(Long vehiculoId) {
        return seguroRepository.findByVehiculoIdVehiculo(vehiculoId);
    }
}