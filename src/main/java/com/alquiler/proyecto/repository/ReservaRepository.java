package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Reserva;

public interface ReservaRepository extends JpaRepository<Reserva, Long> {
}
