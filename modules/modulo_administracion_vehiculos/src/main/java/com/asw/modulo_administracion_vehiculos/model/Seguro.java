package com.asw.modulo_administracion_vehiculos.model;

import jakarta.persistence.*;
import lombok.Data;

import java.time.LocalDate;

@Data
@Entity
@Table(name = "seguros")
public class Seguro {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @ManyToOne
    @JoinColumn(name = "vehiculo_id")
    private Vehiculo vehiculo;
    
    private String compania;
    private String tipo_cobertura;
    private LocalDate fecha_inicio;
    private LocalDate fecha_fin;
}