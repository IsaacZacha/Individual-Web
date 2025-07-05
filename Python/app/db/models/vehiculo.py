from sqlalchemy import Column, Integer, String
from app.db.base import Base

class Vehiculo(Base):
    __tablename__ = "vehiculo"
    id_vehiculo = Column(Integer, primary_key=True, index=True)
    placa = Column(String, unique=True, index=True, nullable=False)
    marca = Column(String, nullable=False)
    modelo = Column(String, nullable=False)
    anio = Column(Integer, nullable=False)
    tipo_id = Column(String, nullable=False)  # Simulado como texto
    estado = Column(String, nullable=False)