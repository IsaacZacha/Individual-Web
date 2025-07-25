package com.asw.modulo_administracion_vehiculos.repository;

import com.asw.modulo_administracion_vehiculos.model.entity.Vehiculo;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface VehiculoRepository extends JpaRepository<Vehiculo, Long> {
}