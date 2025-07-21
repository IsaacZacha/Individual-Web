from pydantic import BaseModel
from typing import Optional
from datetime import date


class MultaCreateDTO(BaseModel):
    alquiler_id: int
    motivo: str
    monto: float
    fecha: date


class MultaUpdateDTO(BaseModel):
    alquiler_id: Optional[int] = None
    motivo: Optional[str] = None
    monto: Optional[float] = None
    fecha: Optional[date] = None


class MultaResponseDTO(BaseModel):
    id_multa: int
    alquiler_id: int
    motivo: str
    monto: float
    fecha: date
    
    class Config:
        from_attributes = True
