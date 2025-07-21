from sqlalchemy import Column, String, Date, Float, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base


class Pago(Base):
    __tablename__ = "pago"
    
    id_pago = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    alquiler_id = Column(BigInteger, ForeignKey("alquiler.id_alquiler"))
    fecha = Column(Date)
    monto = Column(Float)
    metodo = Column(String)
    
    # Relaciones
    alquiler = relationship("Alquiler", back_populates="pagos")
