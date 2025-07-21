from pydantic import BaseModel
from typing import Optional


class VehiculoCreateDTO(BaseModel):
    modelo: str
    placa: str


class VehiculoUpdateDTO(BaseModel):
    modelo: Optional[str] = None
    placa: Optional[str] = None


class VehiculoResponseDTO(BaseModel):
    id: int
    modelo: str
    placa: str
    
    class Config:
        from_attributes = True
