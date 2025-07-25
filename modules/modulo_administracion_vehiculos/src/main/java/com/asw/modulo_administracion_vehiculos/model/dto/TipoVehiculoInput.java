package com.asw.modulo_administracion_vehiculos.model.dto;

import lombok.Data;

@Data
public class TipoVehiculoInput {
    private String descripcion;
    private String capacidad;
    private String transmision;
}