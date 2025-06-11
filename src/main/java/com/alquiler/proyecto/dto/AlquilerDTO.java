package com.alquiler.proyecto.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class AlquilerDTO {
    private Long idAlquiler;
    private Long reservaId;
    private LocalDate fechaEntrega;
    private LocalDate fechaDevolucion;
    private Double kilometrajeInicial;
    private Double kilometrajeFinal;
    private Double total;
}