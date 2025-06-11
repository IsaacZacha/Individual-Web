package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Pago;

public interface PagoRepository extends JpaRepository<Pago, Long> {
}
