from pydantic import BaseModel
from typing import Optional
from datetime import date


class PagoCreateDTO(BaseModel):
    alquiler_id: int
    fecha: date
    monto: float
    metodo: str


class PagoUpdateDTO(BaseModel):
    alquiler_id: Optional[int] = None
    fecha: Optional[date] = None
    monto: Optional[float] = None
    metodo: Optional[str] = None


class PagoResponseDTO(BaseModel):
    id_pago: int
    alquiler_id: int
    fecha: date
    monto: float
    metodo: str
    
    class Config:
        from_attributes = True
