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

import com.alquiler.proyecto.dto.InspeccionDTO;
import com.alquiler.proyecto.entity.Alquiler;
import com.alquiler.proyecto.entity.Inspeccion;
import com.alquiler.proyecto.repository.AlquilerRepository;
import com.alquiler.proyecto.service.InspeccionService;

@RestController
@RequestMapping("/inspecciones")
public class InspeccionController {

    @Autowired
    private InspeccionService inspeccionService;
    @Autowired
    private AlquilerRepository alquilerRepository;

    @GetMapping
    public List<InspeccionDTO> obtenerTodas() {
        return inspeccionService.findAll().stream()
            .map(this::toDTO)
            .collect(Collectors.toList());
    }

    @GetMapping("/{id}")
    public InspeccionDTO obtenerPorId(@PathVariable Long id) {
        return inspeccionService.findById(id)
            .map(this::toDTO)
            .orElse(null);
    }

    @PostMapping
    public InspeccionDTO crear(@RequestBody InspeccionDTO dto) {
        Inspeccion inspeccion = toEntity(dto);
        return toDTO(inspeccionService.save(inspeccion));
    }

    @PutMapping("/{id}")
    public InspeccionDTO actualizar(@PathVariable Long id, @RequestBody InspeccionDTO dto) {
        Optional<Inspeccion> insOpt = inspeccionService.findById(id);
        if (insOpt.isPresent()) {
            Inspeccion ins = insOpt.get();
            ins.setFecha(dto.getFecha());
            ins.setObservaciones(dto.getObservaciones());
            ins.setEstadoVehiculo(dto.getEstadoVehiculo());
            Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
            ins.setAlquiler(alquiler);
            return toDTO(inspeccionService.save(ins));
        }
        return null;
    }

    @DeleteMapping("/{id}")
    public void eliminar(@PathVariable Long id) {
        inspeccionService.deleteById(id);
    }

    // Métodos de mapeo
    private InspeccionDTO toDTO(Inspeccion ins) {
        InspeccionDTO dto = new InspeccionDTO();
        dto.setIdInspeccion(ins.getIdInspeccion());
        dto.setAlquilerId(ins.getAlquiler() != null ? ins.getAlquiler().getIdAlquiler() : null);
        dto.setFecha(ins.getFecha());
        dto.setObservaciones(ins.getObservaciones());
        dto.setEstadoVehiculo(ins.getEstadoVehiculo());
        return dto;
    }

    private Inspeccion toEntity(InspeccionDTO dto) {
        Inspeccion ins = new Inspeccion();
        ins.setIdInspeccion(dto.getIdInspeccion());
        ins.setFecha(dto.getFecha());
        ins.setObservaciones(dto.getObservaciones());
        ins.setEstadoVehiculo(dto.getEstadoVehiculo());
        Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
        ins.setAlquiler(alquiler);
        return ins;
    }
}