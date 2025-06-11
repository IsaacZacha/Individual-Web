package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Vehiculo;

public interface VehiculoRepository extends JpaRepository<Vehiculo, Long> {
}