package com.asw.modulo_administracion_vehiculos.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import com.asw.modulo_administracion_vehiculos.model.Seguro;

import java.util.List;

@Repository
public interface SeguroRepository extends JpaRepository<Seguro, Long> {
    List<Seguro> findByVehiculoId(Long vehiculoId);
}