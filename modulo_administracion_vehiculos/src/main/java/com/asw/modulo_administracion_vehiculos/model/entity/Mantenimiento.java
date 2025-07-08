package com.asw.modulo_administracion_vehiculos.model.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;

import java.time.LocalDate;
import lombok.Data;

@Entity
@Data
@Table(name = "mantenimientos")
public class Mantenimiento {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_mantenimiento")
    private Long idMantenimiento;
    
    @ManyToOne
    @JoinColumn(name = "vehiculo_id")
    private Vehiculo vehiculo;
    
    private LocalDate fechaInicio;
    private LocalDate fechaFin;
    private String descripcion;
    private Double costo;
}