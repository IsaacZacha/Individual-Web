from sqlalchemy import Column, Integer, Date, Float, ForeignKey, String
from . import db

class Pago(db.Model):
    __tablename__ = 'pago'
    id_pago = Column(Integer, primary_key=True)
    alquiler_id = Column(Integer, ForeignKey('alquiler.id_alquiler'))
    fecha = Column(Date)
    monto = Column(Float)
    metodo = Column(String)
