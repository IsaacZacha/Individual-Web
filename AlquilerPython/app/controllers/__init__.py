from .cliente_controller import router as cliente_router
from .vehiculo_controller import router as vehiculo_router
from .reserva_controller import router as reserva_router
from .alquiler_controller import router as alquiler_router
from .pago_controller import router as pago_router
from .multa_controller import router as multa_router
from .inspeccion_controller import router as inspeccion_router

__all__ = [
    "cliente_router",
    "vehiculo_router",
    "reserva_router",
    "alquiler_router",
    "pago_router",
    "multa_router",
    "inspeccion_router"
]
