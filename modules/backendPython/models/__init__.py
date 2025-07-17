from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

from .pago import Pago
from .reserva import Reserva
from .multa import Multa
from .inspeccion import Inspeccion
from .alquiler import Alquiler
