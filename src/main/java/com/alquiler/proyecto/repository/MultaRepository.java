package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Multa;

public interface MultaRepository extends JpaRepository<Multa, Long> {
}
