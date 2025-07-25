import { gql } from "@apollo/client"

export const SAMPLE_QUERIES = {
  GET_ALL_CLIENTES: gql`
    query GetAllClientes {
      clientes {
        idCliente
        nombre
        apellido
        cedulaPasaporte
        direccion
        correo
        telefono
        licenciasConduccion {
          idLicencia
          numero
          paisEmision
          fechaEmision
          fechaVencimiento
        }
        tarjetasPago {
          idTarjeta
          numeroTarjeta
          tipo
          fechaExpiracion
        }
        evaluaciones {
          idEvaluacion
          fecha
          puntaje
          comentarios
        }
      }
    }
  `,

  GET_ALL_VEHICULOS: gql`
    query GetAllVehiculos {
      vehiculos {
        id_vehiculo
        placa
        marca
        modelo
        anio
        estado
        tipo {
          id_tipo
          descripcion
          capacidad
          transmision
        }
        mantenimientos {
          id_mantenimiento
          fecha_inicio
          fecha_fin
          descripcion
          costo
        }
        seguros {
          id_seguro
          compania
          tipo_cobertura
          fecha_inicio
          fecha_fin
        }
      }
    }
  `,

  GET_ALL_EMPLEADOS: gql`
    query GetAllEmpleados {
      empleados {
        id_empleado
        nombre
        cargo
        correo
        telefono
        created_at
        updated_at
      }
    }
  `,

  GET_ALL_RESERVAS: gql`
    query GetAllReservas {
      allReservas {
        id_reserva
        cliente_id
        vehiculo_id
        fecha_reserva
        fecha_inicio
        fecha_fin
        estado
      }
    }
  `,

  GET_ALL_ALQUILERES: gql`
    query GetAllAlquileres {
      allAlquileres {
        id_alquiler
        fecha_entrega
        fecha_devolucion
        kilometraje_inicial
        kilometraje_final
        total
        reserva {
          id_reserva
          cliente_id
          vehiculo_id
          estado
        }
      }
    }
  `,

  GET_CLIENTE_BY_ID: gql`
    query GetClienteById($id: Int!) {
      clienteById(id: $id) {
        idCliente
        nombre
        apellido
        cedulaPasaporte
        direccion
        correo
        telefono
        licenciasConduccion {
          numero
          paisEmision
          fechaVencimiento
        }
        historialAlquileres {
          idHistorial
          vehiculoId
          fechaInicio
          fechaFin
          totalPagado
        }
      }
    }
  `,
}

export const SAMPLE_MUTATIONS = {
  CREATE_CLIENTE: gql`
    mutation CreateCliente($input: ClienteInput!) {
      addCliente(input: $input) {
        idCliente
        nombre
        apellido
        cedulaPasaporte
        direccion
        correo
        telefono
      }
    }
  `,

  CREATE_RESERVA: gql`
    mutation CreateReserva(
      $cliente_id: Int!
      $vehiculo_id: Int!
      $fecha_inicio: String!
      $fecha_fin: String!
      $estado: String!
    ) {
      createReserva(
        cliente_id: $cliente_id
        vehiculo_id: $vehiculo_id
        fecha_inicio: $fecha_inicio
        fecha_fin: $fecha_fin
        estado: $estado
      ) {
        id_reserva
        cliente_id
        vehiculo_id
        fecha_inicio
        fecha_fin
        estado
      }
    }
  `,

  CREATE_VEHICULO: gql`
    mutation CreateVehiculo(
      $placa: String!
      $marca: String!
      $modelo: String!
      $anio: Int!
      $estado: String!
      $tipoId: ID!
    ) {
      crearVehiculo(
        placa: $placa
        marca: $marca
        modelo: $modelo
        anio: $anio
        estado: $estado
        tipoId: $tipoId
      ) {
        id_vehiculo
        placa
        marca
        modelo
        anio
        estado
      }
    }
  `,
}
