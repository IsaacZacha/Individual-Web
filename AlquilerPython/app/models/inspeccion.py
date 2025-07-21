from sqlalchemy import Column, String, Date, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


class Inspeccion(Base):
    __tablename__ = "inspeccion"
    
    id_inspeccion = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    fecha = Column(Date)
    observaciones = Column(String)
    estado_vehiculo = Column(String)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="inspecciones")
