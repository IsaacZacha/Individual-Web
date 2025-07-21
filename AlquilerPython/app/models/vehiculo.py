from sqlalchemy import Column, String, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


class Vehiculo(Base):
    __tablename__ = "vehiculo"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    modelo = Column(String, index=True)
    placa = Column(String, unique=True, index=True)
    
    # Relaciones
    reservas = relationship("Reserva", back_populates="vehiculo")
