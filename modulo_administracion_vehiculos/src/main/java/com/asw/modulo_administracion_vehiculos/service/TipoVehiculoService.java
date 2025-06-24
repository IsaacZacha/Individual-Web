package com.asw.modulo_administracion_vehiculos.service;

import com.asw.modulo_administracion_vehiculos.model.TipoVehiculo;
import com.asw.modulo_administracion_vehiculos.repository.TipoVehiculoRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class TipoVehiculoService {
    private final TipoVehiculoRepository tipoVehiculoRepository;

    public TipoVehiculoService(TipoVehiculoRepository tipoVehiculoRepository) {
        this.tipoVehiculoRepository = tipoVehiculoRepository;
    }

    public List<TipoVehiculo> getAllTiposVehiculo() {
        return tipoVehiculoRepository.findAll();
    }
}