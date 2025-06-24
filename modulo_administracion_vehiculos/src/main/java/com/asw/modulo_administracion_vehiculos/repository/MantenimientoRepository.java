package com.asw.modulo_administracion_vehiculos.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.asw.modulo_administracion_vehiculos.model.Mantenimiento;

import java.util.List;

@Repository
public interface MantenimientoRepository extends JpaRepository<Mantenimiento, Long> {
    List<Mantenimiento> findByVehiculoId(Long vehiculoId);
}