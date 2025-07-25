package com.asw.modulo_administracion_vehiculos.dto;


import lombok.Data;

@Data
public class VehiculoDTO {
    private String placa;
    private String marca;
    private String modelo;
    private Integer anio;
    private Long tipo_id;
    private String estado;


    
}