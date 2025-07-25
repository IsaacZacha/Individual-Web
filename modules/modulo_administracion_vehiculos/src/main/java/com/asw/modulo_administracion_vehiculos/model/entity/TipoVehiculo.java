package com.asw.modulo_administracion_vehiculos.model.entity;

import jakarta.persistence.*;
import lombok.Data;

import java.util.List;

@Data
@Entity
@Table(name = "tipos_vehiculo")
public class TipoVehiculo {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_tipo")
    private Long idTipo;
    
    private String descripcion;
    private String capacidad;
    private String transmision;
    
    @OneToMany(mappedBy = "tipo")
    private List<Vehiculo> vehiculos;
}