package com.asw.modulo_administracion_vehiculos.model;

import jakarta.persistence.*;
import lombok.Data;

import java.util.List;

@Data
@Entity
@Table(name = "vehiculos")
public class Vehiculo {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    private String placa;
    private String marca;
    private String modelo;
    private Integer anio;
    
    @ManyToOne
    @JoinColumn(name = "tipo_id")
    private TipoVehiculo tipo;
    
    private String estado;
    
    @OneToMany(mappedBy = "vehiculo", cascade = CascadeType.ALL)
    private List<Mantenimiento> mantenimientos;
    
    @OneToMany(mappedBy = "vehiculo", cascade = CascadeType.ALL)
    private List<Seguro> seguros;
    
    @OneToOne(mappedBy = "vehiculo", cascade = CascadeType.ALL)
    private Combustible combustible;
}