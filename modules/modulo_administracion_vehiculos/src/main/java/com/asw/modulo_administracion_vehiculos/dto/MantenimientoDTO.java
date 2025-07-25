package com.asw.modulo_administracion_vehiculos.dto;

import lombok.Data;

import java.time.LocalDate;

@Data
public class MantenimientoDTO {
    private Long vehiculo_id;
    private LocalDate fecha_inicio;
    private LocalDate fecha_fin;
    private String descripcion;
    private Double costo;
}