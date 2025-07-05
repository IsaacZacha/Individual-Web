from sqlalchemy import Column, Integer, String
from app.db.base import Base

class Sucursal(Base):
    __tablename__ = "sucursal"
    id_sucursal = Column(Integer, primary_key=True, index=True)
    nombre = Column(String)
    direccion = Column(String)
    ciudad = Column(String)
    telefono = Column(String)