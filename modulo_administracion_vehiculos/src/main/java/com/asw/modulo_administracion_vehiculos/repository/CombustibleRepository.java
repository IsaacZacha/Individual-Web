package com.asw.modulo_administracion_vehiculos.repository;


import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.asw.modulo_administracion_vehiculos.model.Combustible;

@Repository
public interface CombustibleRepository extends JpaRepository<Combustible, Long> {
    Combustible findByVehiculoId(Long vehiculoId);
}