from sqlalchemy import Column, Integer, ForeignKey, Date
from app.db.base import Base

class VehiculoSucursal(Base):
    __tablename__ = "vehiculo_sucursal"
    id_relacion = Column(Integer, primary_key=True, index=True)
    vehiculo_id = Column(Integer, ForeignKey("vehiculo.id_vehiculo"))
    sucursal_id = Column(Integer, ForeignKey("sucursal.id_sucursal"))
    fecha_ingreso = Column(Date)