from sqlalchemy import Column, Integer, String, Date, Float, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base

class Cliente(Base):
    __tablename__ = "cliente"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String, index=True)
    email = Column(String, index=True)
    
    # Relaciones
    reservas = relationship("Reserva", back_populates="cliente")

class Vehiculo(Base):
    __tablename__ = "vehiculo"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    modelo = Column(String, index=True)
    placa = Column(String, unique=True, index=True)
    
    # Relaciones
    reservas = relationship("Reserva", back_populates="vehiculo")

class Reserva(Base):
    __tablename__ = "reserva"
    
    id_reserva = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    cliente_id = Column(BigInteger, ForeignKey("cliente.id"), nullable=False)
    vehiculo_id = Column(BigInteger, ForeignKey("vehiculo.id"), nullable=False)
    fecha_reserva = Column(Date)
    fecha_inicio = Column(Date)
    fecha_fin = Column(Date)
    estado = Column(String)
    
    # Relaciones
    cliente = relationship("Cliente", back_populates="reservas")
    vehiculo = relationship("Vehiculo", back_populates="reservas")
    alquiler = relationship("Alquiler", back_populates="reserva", uselist=False)

class Alquiler(Base):
    __tablename__ = "alquiler"
    
    id_alquiler = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    reserva_id = Column(BigInteger, ForeignKey("reserva.id_reserva"), unique=True)
    fecha_entrega = Column(Date)
    fecha_devolucion = Column(Date)
    kilometraje_inicial = Column(Float)
    kilometraje_final = Column(Float)
    total = Column(Float)
    
    # Relaciones
    reserva = relationship("Reserva", back_populates="alquiler")
    pagos = relationship("Pago", back_populates="alquiler")
    multas = relationship("Multa", back_populates="alquiler")
    inspecciones = relationship("Inspeccion", back_populates="alquiler")

class Pago(Base):
    __tablename__ = "pago"
    
    id_pago = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    fecha = Column(Date)
    monto = Column(Float)
    metodo = Column(String)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="pagos")

class Multa(Base):
    __tablename__ = "multa"
    
    id_multa = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    motivo = Column(String)
    monto = Column(Float)
    fecha = Column(Date)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="multas")

class Inspeccion(Base):
    __tablename__ = "inspeccion"
    
    id_inspeccion = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    fecha = Column(Date)
    observaciones = Column(String)
    estado_vehiculo = Column(String)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="inspecciones")
