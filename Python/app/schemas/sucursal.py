from pydantic import BaseModel

class SucursalBase(BaseModel):
    nombre: str
    direccion: str
    ciudad: str
    telefono: str

class SucursalCreate(SucursalBase):
    pass

class SucursalOut(SucursalBase):
    id_sucursal: int

    model_config = {
    "from_attributes": True
    }