from pydantic import BaseModel
from typing import Optional
from datetime import date


class ReservaCreateDTO(BaseModel):
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: date
    fecha_inicio: date
    fecha_fin: date
    estado: str


class ReservaUpdateDTO(BaseModel):
    cliente_id: Optional[int] = None
    vehiculo_id: Optional[int] = None
    fecha_reserva: Optional[date] = None
    fecha_inicio: Optional[date] = None
    fecha_fin: Optional[date] = None
    estado: Optional[str] = None


class ReservaResponseDTO(BaseModel):
    id_reserva: int
    cliente_id: int
    vehiculo_id: int
    fecha_reserva: date
    fecha_inicio: date
    fecha_fin: date
    estado: str
    
    class Config:
        from_attributes = True
