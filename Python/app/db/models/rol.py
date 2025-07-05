from sqlalchemy import Column, Integer, String
from app.db.base import Base

class Rol(Base):
    __tablename__ = "rol"
    id_rol = Column(Integer, primary_key=True, index=True)
    nombre = Column(String)