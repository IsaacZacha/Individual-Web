package com.alquiler.proyecto.service;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.alquiler.proyecto.entity.Alquiler;
import com.alquiler.proyecto.repository.AlquilerRepository;

@Service
public class AlquilerService {

    @Autowired
    private AlquilerRepository alquilerRepository;

    public List<Alquiler> findAll() {
        return alquilerRepository.findAll();
    }

    public Optional<Alquiler> findById(Long id) {
        return alquilerRepository.findById(id);
    }

    public Alquiler save(Alquiler alquiler) {
        return alquilerRepository.save(alquiler);
    }

    public void deleteById(Long id) {
        alquilerRepository.deleteById(id);
    }
}