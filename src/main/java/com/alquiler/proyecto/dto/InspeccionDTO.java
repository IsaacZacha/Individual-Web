package com.alquiler.proyecto.dto;

import java.time.LocalDate;

import lombok.Data;

@Data
public class InspeccionDTO {
    private Long idInspeccion;
    private Long alquilerId;
    private LocalDate fecha;
    private String observaciones;
    private String estadoVehiculo;
}