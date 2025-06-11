package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Alquiler;



public interface AlquilerRepository extends JpaRepository<Alquiler, Long> {
}
