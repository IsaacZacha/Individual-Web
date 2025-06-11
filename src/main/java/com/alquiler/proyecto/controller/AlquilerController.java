package com.alquiler.proyecto.controller;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.alquiler.proyecto.dto.AlquilerDTO;
import com.alquiler.proyecto.entity.Alquiler;
import com.alquiler.proyecto.entity.Reserva;
import com.alquiler.proyecto.repository.ReservaRepository;
import com.alquiler.proyecto.service.AlquilerService;

@RestController
@RequestMapping("/alquileres")
public class AlquilerController {

    @Autowired
    private AlquilerService alquilerService;
    @Autowired
    private ReservaRepository reservaRepository;

    @GetMapping
    public List<AlquilerDTO> obtenerTodos() {
        return alquilerService.findAll().stream()
            .map(this::toDTO)
            .collect(Collectors.toList());
    }

    @GetMapping("/{id}")
    public AlquilerDTO obtenerPorId(@PathVariable Long id) {
        return alquilerService.findById(id)
            .map(this::toDTO)
            .orElse(null);
    }

    @PostMapping
    public AlquilerDTO crear(@RequestBody AlquilerDTO dto) {
        Alquiler alquiler = toEntity(dto);
        return toDTO(alquilerService.save(alquiler));
    }

    @PutMapping("/{id}")
    public AlquilerDTO actualizar(@PathVariable Long id, @RequestBody AlquilerDTO dto) {
        Optional<Alquiler> alquilerOpt = alquilerService.findById(id);
        if (alquilerOpt.isPresent()) {
            Alquiler alquiler = alquilerOpt.get();
            alquiler.setFechaEntrega(dto.getFechaEntrega());
            alquiler.setFechaDevolucion(dto.getFechaDevolucion());
            alquiler.setKilometrajeInicial(dto.getKilometrajeInicial());
            alquiler.setKilometrajeFinal(dto.getKilometrajeFinal());
            alquiler.setTotal(dto.getTotal());
            Reserva reserva = reservaRepository.findById(dto.getReservaId()).orElse(null);
            alquiler.setReserva(reserva);
            return toDTO(alquilerService.save(alquiler));
        }
        return null;
    }

    @DeleteMapping("/{id}")
    public void eliminar(@PathVariable Long id) {
        alquilerService.deleteById(id);
    }

    // Métodos de mapeo
    private AlquilerDTO toDTO(Alquiler alquiler) {
        AlquilerDTO dto = new AlquilerDTO();
        dto.setIdAlquiler(alquiler.getIdAlquiler());
        dto.setReservaId(alquiler.getReserva() != null ? alquiler.getReserva().getIdReserva() : null);
        dto.setFechaEntrega(alquiler.getFechaEntrega());
        dto.setFechaDevolucion(alquiler.getFechaDevolucion());
        dto.setKilometrajeInicial(alquiler.getKilometrajeInicial());
        dto.setKilometrajeFinal(alquiler.getKilometrajeFinal());
        dto.setTotal(alquiler.getTotal());
        return dto;
    }

    private Alquiler toEntity(AlquilerDTO dto) {
        Alquiler alquiler = new Alquiler();
        alquiler.setIdAlquiler(dto.getIdAlquiler());
        alquiler.setFechaEntrega(dto.getFechaEntrega());
        alquiler.setFechaDevolucion(dto.getFechaDevolucion());
        alquiler.setKilometrajeInicial(dto.getKilometrajeInicial());
        alquiler.setKilometrajeFinal(dto.getKilometrajeFinal());
        alquiler.setTotal(dto.getTotal());
        Reserva reserva = reservaRepository.findById(dto.getReservaId()).orElse(null);
        alquiler.setReserva(reserva);
        return alquiler;
    }
}