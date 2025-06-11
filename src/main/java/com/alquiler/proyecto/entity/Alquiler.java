package com.alquiler.proyecto.entity;

import java.time.LocalDate;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.OneToOne;
import lombok.Data;

@Entity
@Data
public class Alquiler {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long idAlquiler;

    @OneToOne
    @JoinColumn(name = "reserva_id", referencedColumnName = "idReserva")
    private Reserva reserva;

    private LocalDate fechaEntrega;
    private LocalDate fechaDevolucion;

    private Double kilometrajeInicial;
    private Double kilometrajeFinal;
    private Double total;
}
