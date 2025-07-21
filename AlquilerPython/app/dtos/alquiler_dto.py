from pydantic import BaseModel
from typing import Optional
from datetime import date


class AlquilerCreateDTO(BaseModel):
    reserva_id: int
    fecha_entrega: date
    fecha_devolucion: Optional[date] = None
    kilometraje_inicial: float
    kilometraje_final: Optional[float] = None
    total: float


class AlquilerUpdateDTO(BaseModel):
    reserva_id: Optional[int] = None
    fecha_entrega: Optional[date] = None
    fecha_devolucion: Optional[date] = None
    kilometraje_inicial: Optional[float] = None
    kilometraje_final: Optional[float] = None
    total: Optional[float] = None


class AlquilerResponseDTO(BaseModel):
    id_alquiler: int
    reserva_id: int
    fecha_entrega: date
    fecha_devolucion: Optional[date] = None
    kilometraje_inicial: float
    kilometraje_final: Optional[float] = None
    total: float
    
    class Config:
        from_attributes = True
