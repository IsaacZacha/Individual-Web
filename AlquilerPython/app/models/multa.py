from sqlalchemy import Column, String, Date, Float, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


class Multa(Base):
    __tablename__ = "multa"
    
    id_multa = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    motivo = Column(String)
    monto = Column(Float)
    fecha = Column(Date)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="multas")
