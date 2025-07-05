from pydantic import BaseModel

class EmpleadoBase(BaseModel):
    nombre: str
    cargo: str
    correo: str
    telefono: str

class EmpleadoCreate(EmpleadoBase):
    pass

class EmpleadoOut(EmpleadoBase):
    id_empleado: int

    model_config = {
        "from_attributes": True
    }