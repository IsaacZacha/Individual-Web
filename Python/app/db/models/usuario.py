from sqlalchemy import Column, Integer, String, ForeignKey
from app.db.base import Base

class Usuario(Base):
    __tablename__ = "usuario"
    id_usuario = Column(Integer, primary_key=True, index=True)
    empleado_id = Column(Integer, ForeignKey("empleado.id_empleado"))
    username = Column(String, unique=True, index=True)
    contrasena_hash = Column(String)
    rol_id = Column(Integer, ForeignKey("rol.id_rol"))