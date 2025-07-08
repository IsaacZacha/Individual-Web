package com.asw.modulo_administracion_vehiculos.model.entity;


import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import lombok.Data;

@Entity
@Data
@Table(name = "vehiculos")
public class Vehiculo {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_vehiculo")
    private Long idVehiculo;

    private String placa;
    private String marca;
    private String modelo;
    private Integer anio;
    private String estado;

    @ManyToOne
    @JoinColumn(name = "tipo_id")
    private TipoVehiculo tipo;
}