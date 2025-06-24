package com.asw.modulo_administracion_vehiculos.service;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.asw.modulo_administracion_vehiculos.dto.VehiculoDTO;
import com.asw.modulo_administracion_vehiculos.model.Vehiculo;
import com.asw.modulo_administracion_vehiculos.repository.TipoVehiculoRepository;
import com.asw.modulo_administracion_vehiculos.repository.VehiculoRepository;

import java.util.List;

@Service
public class VehiculoService {

    @Autowired
    private VehiculoRepository vehiculoRepository;
    
    @Autowired
    private TipoVehiculoRepository tipoVehiculoRepository;
    
    @Autowired
    private NatsService natsService;

    public List<Vehiculo> getAllVehiculos() {
        return vehiculoRepository.findAll();
    }

    public Vehiculo getVehiculoById(Long id) {
        return vehiculoRepository.findById(id).orElse(null);
    }

    @Transactional
    public Vehiculo createVehiculo(VehiculoDTO vehiculoDTO) {
        Vehiculo vehiculo = new Vehiculo();
        vehiculo.setPlaca(vehiculoDTO.getPlaca());
        vehiculo.setMarca(vehiculoDTO.getMarca());
        vehiculo.setModelo(vehiculoDTO.getModelo());
        vehiculo.setAnio(vehiculoDTO.getAnio());
        vehiculo.setEstado(vehiculoDTO.getEstado());
        
        tipoVehiculoRepository.findById(vehiculoDTO.getTipo_id()).ifPresent(vehiculo::setTipo);
        
        Vehiculo savedVehiculo = vehiculoRepository.save(vehiculo);
        
        // Publicar evento NATS
        natsService.publishEvent("vehiculo.creado", "Nuevo vehículo creado: " + savedVehiculo.getId());
        
        return savedVehiculo;
    }

    @Transactional
    public Vehiculo updateVehiculo(Long id, VehiculoDTO vehiculoDTO) {
        return vehiculoRepository.findById(id).map(vehiculo -> {
            vehiculo.setPlaca(vehiculoDTO.getPlaca());
            vehiculo.setMarca(vehiculoDTO.getMarca());
            vehiculo.setModelo(vehiculoDTO.getModelo());
            vehiculo.setAnio(vehiculoDTO.getAnio());
            vehiculo.setEstado(vehiculoDTO.getEstado());
            
            tipoVehiculoRepository.findById(vehiculoDTO.getTipo_id()).ifPresent(vehiculo::setTipo);
            
            Vehiculo updatedVehiculo = vehiculoRepository.save(vehiculo);
            
            // Publicar evento NATS
            natsService.publishEvent("vehiculo.actualizado", "Vehículo actualizado: " + updatedVehiculo.getId());
            
            return updatedVehiculo;
        }).orElse(null);
    }

    public boolean deleteVehiculo(Long id) {
        if (vehiculoRepository.existsById(id)) {
            vehiculoRepository.deleteById(id);
            
            // Publicar evento NATS
            natsService.publishEvent("vehiculo.eliminado", "Vehículo eliminado: " + id);
            
            return true;
        }
        return false;
    }

    public List<Vehiculo> getVehiculosByEstado(String estado) {
        return vehiculoRepository.findByEstado(estado);
    }
}