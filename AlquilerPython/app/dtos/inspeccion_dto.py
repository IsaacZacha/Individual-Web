from pydantic import BaseModel
from typing import Optional
from datetime import date


class InspeccionCreateDTO(BaseModel):
    alquiler_id: int
    fecha: date
    observaciones: str
    estado_vehiculo: str


class InspeccionUpdateDTO(BaseModel):
    alquiler_id: Optional[int] = None
    fecha: Optional[date] = None
    observaciones: Optional[str] = None
    estado_vehiculo: Optional[str] = None


class InspeccionResponseDTO(BaseModel):
    id_inspeccion: int
    alquiler_id: int
    fecha: date
    observaciones: str
    estado_vehiculo: str
    
    class Config:
        from_attributes = True
