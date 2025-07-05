from pydantic import BaseModel

class RolBase(BaseModel):
    nombre: str

class RolCreate(RolBase):
    pass

class RolOut(RolBase):
    id_rol: int

    model_config = {
    "from_attributes": True
    }