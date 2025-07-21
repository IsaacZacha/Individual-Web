from sqlalchemy import Column, String, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


class Cliente(Base):
    __tablename__ = "cliente"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String, index=True)
    email = Column(String, index=True)
    
    # Relaciones
    reservas = relationship("Reserva", back_populates="cliente")
