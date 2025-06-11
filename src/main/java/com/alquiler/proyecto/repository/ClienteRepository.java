package com.alquiler.proyecto.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import com.alquiler.proyecto.entity.Cliente;

public interface ClienteRepository extends JpaRepository<Cliente, Long> {
}