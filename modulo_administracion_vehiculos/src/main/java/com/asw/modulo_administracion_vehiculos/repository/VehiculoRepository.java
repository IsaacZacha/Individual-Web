package com.asw.modulo_administracion_vehiculos.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.asw.modulo_administracion_vehiculos.model.Vehiculo;

import java.util.List;

@Repository
public interface VehiculoRepository extends JpaRepository<Vehiculo, Long> {
    List<Vehiculo> findByEstado(String estado);
    Vehiculo findByPlaca(String placa);
}