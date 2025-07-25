package com.asw.modulo_administracion_vehiculos.model.entity;
import jakarta.persistence.*;
import lombok.Data;

import java.time.LocalDate;

@Data
@Entity
@Table(name = "mantenimientos")
public class Mantenimiento {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_mantenimiento")
    private Long idMantenimiento;
    
    @ManyToOne
    @JoinColumn(name = "vehiculo_id", referencedColumnName = "id_vehiculo")
    private Vehiculo vehiculo;
    
    private LocalDate fechaInicio;
    private LocalDate fechaFin;
    private String descripcion;
    private Double costo;
}