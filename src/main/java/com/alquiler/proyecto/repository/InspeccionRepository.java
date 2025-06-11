package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Inspeccion;

public interface InspeccionRepository extends JpaRepository<Inspeccion, Long> {
}
