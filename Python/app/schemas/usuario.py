from pydantic import BaseModel

class UsuarioBase(BaseModel):
    empleado_id: int
    username: str
    rol_id: int

class UsuarioCreate(UsuarioBase):
    contrasena: str

class UsuarioOut(UsuarioBase):
    id_usuario: int

    model_config = {
    "from_attributes": True
    }