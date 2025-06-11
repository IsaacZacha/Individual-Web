package com.alquiler.proyecto.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class MultaDTO {
    private Long idMulta;
    private Long alquilerId;
    private String motivo;
    private Double monto;
    private LocalDate fecha;
}