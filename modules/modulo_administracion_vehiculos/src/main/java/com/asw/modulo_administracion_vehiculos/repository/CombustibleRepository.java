package com.asw.modulo_administracion_vehiculos.repository;

import com.asw.modulo_administracion_vehiculos.model.entity.Combustible;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface CombustibleRepository extends JpaRepository<Combustible, Long> {
    List<Combustible> findByVehiculoIdVehiculo(Long idVehiculo);
    
    // Consulta adicional para buscar por tipo de combustible
    List<Combustible> findByTipo(String tipo);
    
    // Consulta para vehículos con consumo eficiente (menor a cierto valor)
    List<Combustible> findByConsumoLitros100kmLessThan(Double consumoMaximo);
}