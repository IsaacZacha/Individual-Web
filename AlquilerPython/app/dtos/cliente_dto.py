from pydantic import BaseModel
from typing import Optional


class ClienteCreateDTO(BaseModel):
    nombre: str
    email: str


class ClienteUpdateDTO(BaseModel):
    nombre: Optional[str] = None
    email: Optional[str] = None


class ClienteResponseDTO(BaseModel):
    id: int
    nombre: str
    email: str
    
    class Config:
        from_attributes = True
