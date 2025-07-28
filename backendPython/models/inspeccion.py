from sqlalchemy import Column, Integer, String, Date, ForeignKey
from . import db

class Inspeccion(db.Model):
    __tablename__ = 'inspeccion'
    id_inspeccion = Column(Integer, primary_key=True)
    alquiler_id = Column(Integer, ForeignKey('alquiler.id_alquiler'))
    fecha = Column(Date)
    observaciones = Column(String)
    estado_vehiculo = Column(String)
