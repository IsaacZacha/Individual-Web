from .cliente_dto import ClienteCreateDTO, ClienteUpdateDTO, ClienteResponseDTO
from .vehiculo_dto import VehiculoCreateDTO, VehiculoUpdateDTO, VehiculoResponseDTO
from .reserva_dto import ReservaCreateDTO, ReservaUpdateDTO, ReservaResponseDTO
from .alquiler_dto import AlquilerCreateDTO, AlquilerUpdateDTO, AlquilerResponseDTO
from .pago_dto import PagoCreateDTO, PagoUpdateDTO, PagoResponseDTO
from .multa_dto import MultaCreateDTO, MultaUpdateDTO, MultaResponseDTO
from .inspeccion_dto import InspeccionCreateDTO, InspeccionUpdateDTO, InspeccionResponseDTO

__all__ = [
    "ClienteCreateDTO", "ClienteUpdateDTO", "ClienteResponseDTO",
    "VehiculoCreateDTO", "VehiculoUpdateDTO", "VehiculoResponseDTO", 
    "ReservaCreateDTO", "ReservaUpdateDTO", "ReservaResponseDTO",
    "AlquilerCreateDTO", "AlquilerUpdateDTO", "AlquilerResponseDTO",
    "PagoCreateDTO", "PagoUpdateDTO", "PagoResponseDTO",
    "MultaCreateDTO", "MultaUpdateDTO", "MultaResponseDTO",
    "InspeccionCreateDTO", "InspeccionUpdateDTO", "InspeccionResponseDTO"
]
