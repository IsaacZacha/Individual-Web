from pydantic import BaseModel
from datetime import date

class VehiculoSucursalBase(BaseModel):
    vehiculo_id: int
    sucursal_id: int
    fecha_ingreso: date

class VehiculoSucursalCreate(VehiculoSucursalBase):
    pass

class VehiculoSucursalOut(VehiculoSucursalBase):
    id_relacion: int

    model_config = {
    "from_attributes": True
    }