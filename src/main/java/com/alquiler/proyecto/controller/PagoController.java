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

import com.alquiler.proyecto.dto.PagoDTO;
import com.alquiler.proyecto.entity.Alquiler;
import com.alquiler.proyecto.entity.Pago;
import com.alquiler.proyecto.repository.AlquilerRepository;
import com.alquiler.proyecto.service.PagoService;

@RestController
@RequestMapping("/pagos")
public class PagoController {

    @Autowired
    private PagoService pagoService;
    @Autowired
    private AlquilerRepository alquilerRepository;

    @GetMapping
    public List<PagoDTO> obtenerTodos() {
        return pagoService.findAll().stream()
            .map(this::toDTO)
            .collect(Collectors.toList());
    }

    @GetMapping("/{id}")
    public PagoDTO obtenerPorId(@PathVariable Long id) {
        return pagoService.findById(id)
            .map(this::toDTO)
            .orElse(null);
    }

    @PostMapping
    public PagoDTO crear(@RequestBody PagoDTO dto) {
        Pago pago = toEntity(dto);
        return toDTO(pagoService.save(pago));
    }

    @PutMapping("/{id}")
    public PagoDTO actualizar(@PathVariable Long id, @RequestBody PagoDTO dto) {
        Optional<Pago> pagoOpt = pagoService.findById(id);
        if (pagoOpt.isPresent()) {
            Pago pago = pagoOpt.get();
            pago.setFecha(dto.getFecha());
            pago.setMonto(dto.getMonto());
            pago.setMetodo(dto.getMetodo());
            Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
            pago.setAlquiler(alquiler);
            return toDTO(pagoService.save(pago));
        }
        return null;
    }

    @DeleteMapping("/{id}")
    public void eliminar(@PathVariable Long id) {
        pagoService.deleteById(id);
    }

    // Métodos de mapeo
    private PagoDTO toDTO(Pago pago) {
        PagoDTO dto = new PagoDTO();
        dto.setIdPago(pago.getIdPago());
        dto.setAlquilerId(pago.getAlquiler() != null ? pago.getAlquiler().getIdAlquiler() : null);
        dto.setFecha(pago.getFecha());
        dto.setMonto(pago.getMonto());
        dto.setMetodo(pago.getMetodo());
        return dto;
    }

    private Pago toEntity(PagoDTO dto) {
        Pago pago = new Pago();
        pago.setIdPago(dto.getIdPago());
        pago.setFecha(dto.getFecha());
        pago.setMonto(dto.getMonto());
        pago.setMetodo(dto.getMetodo());
        Alquiler alquiler = alquilerRepository.findById(dto.getAlquilerId()).orElse(null);
        pago.setAlquiler(alquiler);
        return pago;
    }
}