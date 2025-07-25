package com.asw.modulo_administracion_vehiculos.service;


import org.springframework.stereotype.Service;

import com.asw.modulo_administracion_vehiculos.model.entity.TipoVehiculo;
import com.asw.modulo_administracion_vehiculos.repository.TipoVehiculoRepository;

import java.util.List;

@Service
public class TipoVehiculoService {
    private final TipoVehiculoRepository tipoVehiculoRepository;

    public TipoVehiculoService(TipoVehiculoRepository tipoVehiculoRepository) {
        this.tipoVehiculoRepository = tipoVehiculoRepository;
    }

    public List<TipoVehiculo> findAllTiposVehiculo() {
        return tipoVehiculoRepository.findAll();
    }

    public TipoVehiculo findTipoVehiculoById(Long id) {
        return tipoVehiculoRepository.findById(id).orElse(null);
    }

    public TipoVehiculo saveTipoVehiculo(TipoVehiculo tipoVehiculo) {
        return tipoVehiculoRepository.save(tipoVehiculo);
    }

    public void deleteTipoVehiculo(Long id) {
        tipoVehiculoRepository.deleteById(id);
    }
}