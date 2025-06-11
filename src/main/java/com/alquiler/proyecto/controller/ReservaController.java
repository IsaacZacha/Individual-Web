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

import com.alquiler.proyecto.dto.ReservaDTO;
import com.alquiler.proyecto.entity.Cliente;
import com.alquiler.proyecto.entity.Reserva;
import com.alquiler.proyecto.entity.Vehiculo;
import com.alquiler.proyecto.repository.ClienteRepository;
import com.alquiler.proyecto.repository.VehiculoRepository;
import com.alquiler.proyecto.service.ReservaService;

@RestController
@RequestMapping("/reservas")
public class ReservaController {

    @Autowired
    private ReservaService reservaService;
    @Autowired
    private ClienteRepository clienteRepository;
    @Autowired
    private VehiculoRepository vehiculoRepository;

    @GetMapping
    public List<ReservaDTO> obtenerTodas() {
        return reservaService.findAll().stream()
            .map(this::toDTO)
            .collect(Collectors.toList());
    }

    @GetMapping("/{id}")
    public ReservaDTO obtenerPorId(@PathVariable Long id) {
        return reservaService.findById(id)
            .map(this::toDTO)
            .orElse(null);
    }

    @PostMapping
    public ReservaDTO crear(@RequestBody ReservaDTO dto) {
        Reserva reserva = toEntity(dto);
        return toDTO(reservaService.save(reserva));
    }

    @PutMapping("/{id}")
    public ReservaDTO actualizar(@PathVariable Long id, @RequestBody ReservaDTO dto) {
        Optional<Reserva> reservaOpt = reservaService.findById(id);
        if (reservaOpt.isPresent()) {
            Reserva reserva = reservaOpt.get();
            reserva.setFechaReserva(dto.getFechaReserva());
            reserva.setFechaInicio(dto.getFechaInicio());
            reserva.setFechaFin(dto.getFechaFin());
            reserva.setEstado(dto.getEstado());
            Cliente cliente = clienteRepository.findById(dto.getClienteId()).orElse(null);
            Vehiculo vehiculo = vehiculoRepository.findById(dto.getVehiculoId()).orElse(null);
            reserva.setCliente(cliente);
            reserva.setVehiculo(vehiculo);
            return toDTO(reservaService.save(reserva));
        }
        return null;
    }

    @DeleteMapping("/{id}")
    public void eliminar(@PathVariable Long id) {
        reservaService.deleteById(id);
    }

    // Métodos de mapeo
    private ReservaDTO toDTO(Reserva reserva) {
        ReservaDTO dto = new ReservaDTO();
        dto.setIdReserva(reserva.getIdReserva());
        dto.setClienteId(reserva.getCliente() != null ? reserva.getCliente().getId() : null);
        dto.setVehiculoId(reserva.getVehiculo() != null ? reserva.getVehiculo().getId() : null);
        dto.setFechaReserva(reserva.getFechaReserva());
        dto.setFechaInicio(reserva.getFechaInicio());
        dto.setFechaFin(reserva.getFechaFin());
        dto.setEstado(reserva.getEstado());
        return dto;
    }

    private Reserva toEntity(ReservaDTO dto) {
        Reserva reserva = new Reserva();
        reserva.setIdReserva(dto.getIdReserva());
        reserva.setFechaReserva(dto.getFechaReserva());
        reserva.setFechaInicio(dto.getFechaInicio());
        reserva.setFechaFin(dto.getFechaFin());
        reserva.setEstado(dto.getEstado());
        Cliente cliente = clienteRepository.findById(dto.getClienteId()).orElse(null);
        Vehiculo vehiculo = vehiculoRepository.findById(dto.getVehiculoId()).orElse(null);
        reserva.setCliente(cliente);
        reserva.setVehiculo(vehiculo);
        return reserva;
    }
}