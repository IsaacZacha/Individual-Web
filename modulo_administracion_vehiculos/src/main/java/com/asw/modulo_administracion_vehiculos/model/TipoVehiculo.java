package com.asw.modulo_administracion_vehiculos.model;

import jakarta.persistence.*;
import lombok.Data;

import java.util.List;

@Data
@Entity
@Table(name = "tipos_vehiculo")
public class TipoVehiculo {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    private String descripcion;
    private Integer capacidad;
    private String transmision;
    
    @OneToMany(mappedBy = "tipo")
    private List<Vehiculo> vehiculos;
}