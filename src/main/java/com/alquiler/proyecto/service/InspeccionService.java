package com.alquiler.proyecto.service;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.alquiler.proyecto.entity.Inspeccion;
import com.alquiler.proyecto.repository.InspeccionRepository;

@Service
public class InspeccionService {

    @Autowired
    private InspeccionRepository inspeccionRepository;

    public List<Inspeccion> findAll() {
        return inspeccionRepository.findAll();
    }

    public Optional<Inspeccion> findById(Long id) {
        return inspeccionRepository.findById(id);
    }

    public Inspeccion save(Inspeccion inspeccion) {
        return inspeccionRepository.save(inspeccion);
    }

    public void deleteById(Long id) {
        inspeccionRepository.deleteById(id);
    }
}