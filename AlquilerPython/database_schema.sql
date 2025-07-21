-- Script SQL para crear todas las tablas en Supabase
-- Sistema de Alquiler de Vehículos

-- Tabla: cliente
CREATE TABLE cliente (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255),
    email VARCHAR(255)
);

-- Índices para cliente
CREATE INDEX ix_cliente_id ON cliente(id);
CREATE INDEX ix_cliente_nombre ON cliente(nombre);
CREATE INDEX ix_cliente_email ON cliente(email);

-- Tabla: vehiculo
CREATE TABLE vehiculo (
    id BIGSERIAL PRIMARY KEY,
    modelo VARCHAR(255),
    placa VARCHAR(255) UNIQUE
);

-- Índices para vehiculo
CREATE INDEX ix_vehiculo_id ON vehiculo(id);
CREATE INDEX ix_vehiculo_modelo ON vehiculo(modelo);
CREATE INDEX ix_vehiculo_placa ON vehiculo(placa);

-- Tabla: reserva
CREATE TABLE reserva (
    id_reserva BIGSERIAL PRIMARY KEY,
    cliente_id BIGINT NOT NULL REFERENCES cliente(id),
    vehiculo_id BIGINT NOT NULL REFERENCES vehiculo(id),
    fecha_reserva DATE,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(255)
);

-- Índices para reserva
CREATE INDEX ix_reserva_id_reserva ON reserva(id_reserva);

-- Tabla: alquiler
CREATE TABLE alquiler (
    id_alquiler BIGSERIAL PRIMARY KEY,
    reserva_id BIGINT UNIQUE REFERENCES reserva(id_reserva),
    fecha_entrega DATE,
    fecha_devolucion DATE,
    kilometraje_inicial DECIMAL,
    kilometraje_final DECIMAL,
    total DECIMAL
);

-- Índices para alquiler
CREATE INDEX ix_alquiler_id_alquiler ON alquiler(id_alquiler);

-- Tabla: pago
CREATE TABLE pago (
    id_pago BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler),
    fecha DATE,
    monto DECIMAL,
    metodo VARCHAR(255)
);

-- Índices para pago
CREATE INDEX ix_pago_id_pago ON pago(id_pago);

-- Tabla: multa
CREATE TABLE multa (
    id_multa BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler),
    motivo VARCHAR(255),
    monto DECIMAL,
    fecha DATE
);

-- Índices para multa
CREATE INDEX ix_multa_id_multa ON multa(id_multa);

-- Tabla: inspeccion
CREATE TABLE inspeccion (
    id_inspeccion BIGSERIAL PRIMARY KEY,
    alquiler_id BIGINT REFERENCES alquiler(id_alquiler),
    fecha DATE,
    observaciones VARCHAR(255),
    estado_vehiculo VARCHAR(255)
);

-- Índices para inspeccion
CREATE INDEX ix_inspeccion_id_inspeccion ON inspeccion(id_inspeccion);

-- Datos de ejemplo (opcional)
-- Insertar clientes de ejemplo
INSERT INTO cliente (nombre, email) VALUES 
('Juan Pérez', 'juan.perez@email.com'),
('María García', 'maria.garcia@email.com'),
('Carlos López', 'carlos.lopez@email.com');

-- Insertar vehículos de ejemplo
INSERT INTO vehiculo (modelo, placa) VALUES 
('Toyota Corolla 2023', 'ABC-123'),
('Honda Civic 2022', 'DEF-456'),
('Nissan Sentra 2023', 'GHI-789');

-- Insertar reservas de ejemplo
INSERT INTO reserva (cliente_id, vehiculo_id, fecha_reserva, fecha_inicio, fecha_fin, estado) VALUES 
(1, 1, '2024-01-15', '2024-01-20', '2024-01-25', 'Confirmada'),
(2, 2, '2024-01-16', '2024-01-22', '2024-01-27', 'Pendiente'),
(3, 3, '2024-01-17', '2024-01-24', '2024-01-29', 'Confirmada');
