package com.asw.modulo_administracion_vehiculos.model.dto;

import lombok.Data;

@Data
public class VehiculoInput {
    private String placa;
    private String marca;
    private String modelo;
    private Integer anio;
    private Long tipoId;
    private String estado;
}