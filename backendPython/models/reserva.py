from sqlalchemy import Column, Integer, Date, String
from . import db

class Reserva(db.Model):
    __tablename__ = 'reserva'
    id_reserva = Column(Integer, primary_key=True)
    cliente_id = Column(Integer)
    vehiculo_id = Column(Integer)
    fecha_reserva = Column(Date)
    fecha_inicio = Column(Date)
    fecha_fin = Column(Date)
    estado = Column(String)
