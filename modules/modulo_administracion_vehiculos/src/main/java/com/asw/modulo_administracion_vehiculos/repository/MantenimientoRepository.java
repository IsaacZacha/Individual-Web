package com.asw.modulo_administracion_vehiculos.repository;

import com.asw.modulo_administracion_vehiculos.model.entity.Mantenimiento;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface MantenimientoRepository extends JpaRepository<Mantenimiento, Long> {
    List<Mantenimiento> findByVehiculoIdVehiculo(Long idVehiculo);
}