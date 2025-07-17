from sqlalchemy import Column, Integer, Date, Float, ForeignKey
from . import db

class Alquiler(db.Model):
    __tablename__ = 'alquiler'
    id_alquiler = Column(Integer, primary_key=True)
    reserva_id = Column(Integer, ForeignKey('reserva.id_reserva'))
    fecha_entrega = Column(Date)
    fecha_devolucion = Column(Date)
    kilometraje_inicial = Column(Float)
    kilometraje_final = Column(Float)
    total = Column(Float)
