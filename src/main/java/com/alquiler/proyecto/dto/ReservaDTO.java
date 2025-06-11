package com.alquiler.proyecto.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class ReservaDTO {
    private Long idReserva;
    private Long clienteId;
    private Long vehiculoId;
    private LocalDate fechaReserva;
    private LocalDate fechaInicio;
    private LocalDate fechaFin;
    private String estado;
}