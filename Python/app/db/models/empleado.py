from sqlalchemy import Column, Integer, String
from app.db.base import Base

class Empleado(Base):
    __tablename__ = "empleado"
    id_empleado = Column(Integer, primary_key=True, index=True)
    nombre = Column(String)
    cargo = Column(String)
    correo = Column(String, unique=True)
    telefono = Column(String)