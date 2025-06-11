package com.alquiler.proyecto.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class PagoDTO {
    private Long idPago;
    private Long alquilerId;
    private LocalDate fecha;
    private Double monto;
    private String metodo;
}