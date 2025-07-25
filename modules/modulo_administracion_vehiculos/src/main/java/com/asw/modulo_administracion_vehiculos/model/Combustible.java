package com.asw.modulo_administracion_vehiculos.model;

import jakarta.persistence.*;
import lombok.Data;

@Data
@Entity
@Table(name = "combustibles")
public class Combustible {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @OneToOne
    @JoinColumn(name = "vehiculo_id")
    private Vehiculo vehiculo;
    
    private String tipo;
    private Double consumo_litros_100km;
}