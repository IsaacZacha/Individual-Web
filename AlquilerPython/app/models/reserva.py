from sqlalchemy import Column, String, Date, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


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
