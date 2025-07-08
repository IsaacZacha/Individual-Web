package com.asw.modulo_administracion_vehiculos.repository;

import com.asw.modulo_administracion_vehiculos.model.entity.Seguro;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.util.List;

@Repository
public interface SeguroRepository extends JpaRepository<Seguro, Long> {
    List<Seguro> findByVehiculoIdVehiculo(Long idVehiculo);
    
    // Consulta adicional para buscar seguros por compañía
    List<Seguro> findByCompania(String compania);
    
    // Consulta para seguros activos en un rango de fechas
    List<Seguro> findByFechaInicioLessThanEqualAndFechaFinGreaterThanEqual(
            LocalDate fechaInicio, LocalDate fechaFin);
}