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

import com.alquiler.proyecto.dto.MultaDTO;
import com.alquiler.proyecto.entity.Alquiler;
import com.alquiler.proyecto.entity.Multa;
import com.alquiler.proyecto.repository.AlquilerRepository;
import com.alquiler.proyecto.service.MultaService;

@RestController
@RequestMapping("/multas")
public class MultaController {

    @Autowired
    private MultaService multaService;
    @Autowired
    private AlquilerRepository alquilerRepository;

    @GetMapping
    public List<MultaDTO> obtenerTodas() {
        return multaService.findAll().stream()
            .map(this::toDTO)
            .collect(Collectors.toList());
    }

    @GetMapping("/{id}")
    public MultaDTO obtenerPorId(@PathVariable Long id) {
        return multaService.findById(id)
            .map(this::toDTO)
            .orElse(null);
    }

    @PostMapping
    public MultaDTO crear(@RequestBody MultaDTO dto) {
        Multa multa = toEntity(dto);
        return toDTO(multaService.save(multa));
    }

    @PutMapping("/{id}")
    public MultaDTO actualizar(@PathVariable Long id, @RequestBody MultaDTO dto) {
        Optional<Multa> multaOpt = multaService.findById(id);
        if (multaOpt.isPresent()) {
            Multa multa = multaOpt.get();
            multa.setMotivo(dto.getMotivo());
            multa.setMonto(dto.getMonto());
            multa.setFecha(dto.getFecha());
            Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
            multa.setAlquiler(alquiler);
            return toDTO(multaService.save(multa));
        }
        return null;
    }

    @DeleteMapping("/{id}")
    public void eliminar(@PathVariable Long id) {
        multaService.deleteById(id);
    }

    // Métodos de mapeo
    private MultaDTO toDTO(Multa multa) {
        MultaDTO dto = new MultaDTO();
        dto.setIdMulta(multa.getIdMulta());
        dto.setAlquilerId(multa.getAlquiler() != null ? multa.getAlquiler().getIdAlquiler() : null);
        dto.setMotivo(multa.getMotivo());
        dto.setMonto(multa.getMonto());
        dto.setFecha(multa.getFecha());
        return dto;
    }

    private Multa toEntity(MultaDTO dto) {
        Multa multa = new Multa();
        multa.setIdMulta(dto.getIdMulta());
        multa.setMotivo(dto.getMotivo());
        multa.setMonto(dto.getMonto());
        multa.setFecha(dto.getFecha());
        Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
        multa.setAlquiler(alquiler);
        return multa;
    }
}