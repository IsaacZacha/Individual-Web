-- Solo estructura de tablas para Supabase
-- Sistema de Alquiler de Vehículos

-- Eliminar tablas si existen (en orden inverso por las dependencias)
DROP TABLE IF EXISTS inspeccion CASCADE;
DROP TABLE IF EXISTS multa CASCADE;
DROP TABLE IF EXISTS pago CASCADE;
DROP TABLE IF EXISTS alquiler CASCADE;
DROP TABLE IF EXISTS reserva CASCADE;
DROP TABLE IF EXISTS vehiculo CASCADE;
DROP TABLE IF EXISTS cliente CASCADE;

-- Tabla: cliente
CREATE TABLE cliente (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255),
    email VARCHAR(255)
);

CREATE INDEX ix_cliente_id ON cliente(id);
CREATE INDEX ix_cliente_nombre ON cliente(nombre);
CREATE INDEX ix_cliente_email ON cliente(email);

-- Tabla: vehiculo
CREATE TABLE vehiculo (
    id BIGSERIAL PRIMARY KEY,
    modelo VARCHAR(255),
    placa VARCHAR(255) UNIQUE
);

CREATE INDEX ix_vehiculo_id ON vehiculo(id);
CREATE INDEX ix_vehiculo_modelo ON vehiculo(modelo);
CREATE INDEX ix_vehiculo_placa ON vehiculo(placa);

-- Tabla: reserva
CREATE TABLE reserva (
    id_reserva BIGSERIAL PRIMARY KEY,
    cliente_id BIGINT NOT NULL REFERENCES cliente(id) ON DELETE CASCADE,
    vehiculo_id BIGINT NOT NULL REFERENCES vehiculo(id) ON DELETE CASCADE,
    fecha_reserva DATE,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(255)
);

CREATE INDEX ix_reserva_id_reserva ON reserva(id_reserva);

-- Tabla: alquiler
CREATE TABLE alquiler (
    id_alquiler BIGSERIAL PRIMARY KEY,
    reserva_id BIGINT UNIQUE REFERENCES reserva(id_reserva) ON DELETE CASCADE,
    fecha_entrega DATE,
    fecha_devolucion DATE,
    kilometraje_inicial DECIMAL(10,2),
    kilometraje_final DECIMAL(10,2),
    total DECIMAL(10,2)
);

CREATE INDEX ix_alquiler_id_alquiler ON alquiler(id_alquiler);

-- Tabla: pago
CREATE TABLE pago (
    id_pago BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler) ON DELETE CASCADE,
    fecha DATE,
    monto DECIMAL(10,2),
    metodo VARCHAR(255)
);

CREATE INDEX ix_pago_id_pago ON pago(id_pago);

-- Tabla: multa
CREATE TABLE multa (
    id_multa BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler) ON DELETE CASCADE,
    motivo VARCHAR(255),
    monto DECIMAL(10,2),
    fecha DATE
);

CREATE INDEX ix_multa_id_multa ON multa(id_multa);

-- Tabla: inspeccion
CREATE TABLE inspeccion (
    id_inspeccion BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler) ON DELETE CASCADE,
    fecha DATE,
    observaciones TEXT,
    estado_vehiculo VARCHAR(255)
);

CREATE INDEX ix_inspeccion_id_inspeccion ON inspeccion(id_inspeccion);
