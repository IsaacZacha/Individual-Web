// package com.asw.modulo_administracion_vehiculos.mappers;

// import com.asw.modulo_administracion_vehiculos.dto.VehiculoDTO;
// import com.asw.modulo_administracion_vehiculos.model.entity.Vehiculo;
// import org.springframework.stereotype.Component;

// @Component
// public class VehiculoMapper {

//     public VehiculoDTO toDto(Vehiculo vehiculo) {
//         VehiculoDTO dto = new VehiculoDTO();
//         dto.setId(vehiculo.getId());
//         dto.setPlaca(vehiculo.getPlaca());
//         dto.setMarca(vehiculo.getMarca());
//         dto.setModelo(vehiculo.getModelo());
//         dto.setAnio(vehiculo.getAnio());
//         dto.setTipo_id(vehiculo.getTipo().getId());
//         dto.setEstado(vehiculo.getEstado());
//         return dto;
//     }

//     public Vehiculo toEntity(VehiculoDTO dto) {
//         Vehiculo vehiculo = new Vehiculo();
//         vehiculo.setId(dto.getId());
//         vehiculo.setPlaca(dto.getPlaca());
//         vehiculo.setMarca(dto.getMarca());
//         vehiculo.setModelo(dto.getModelo());
//         vehiculo.setAnio(dto.getAnio());
//         // El tipo se debe establecer en el servicio después de buscar por ID
//         vehiculo.setEstado(dto.getEstado());
//         return vehiculo;
//     }
// }