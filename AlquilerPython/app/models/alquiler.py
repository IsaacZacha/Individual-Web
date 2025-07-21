from sqlalchemy import Column, Date, Float, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


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
