from sqlalchemy import Column, Integer, String, Float, Date, ForeignKey
from . import db

class Multa(db.Model):
    __tablename__ = 'multa'
    id_multa = Column(Integer, primary_key=True)
    alquiler_id = Column(Integer, ForeignKey('alquiler.id_alquiler'))
    motivo = Column(String)
    monto = Column(Float)
    fecha = Column(Date)
