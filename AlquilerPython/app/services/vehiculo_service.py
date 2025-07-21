from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.vehiculo import Vehiculo
from app.dtos.vehiculo_dto import VehiculoCreateDTO, VehiculoUpdateDTO


class VehiculoService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Vehiculo]:
        return self.db.query(Vehiculo).all()
    
    def get_by_id(self, vehiculo_id: int) -> Optional[Vehiculo]:
        return self.db.query(Vehiculo).filter(Vehiculo.id == vehiculo_id).first()
    
    def create(self, vehiculo_data: VehiculoCreateDTO) -> Vehiculo:
        vehiculo = Vehiculo(
            modelo=vehiculo_data.modelo,
            placa=vehiculo_data.placa
        )
        self.db.add(vehiculo)
        self.db.commit()
        self.db.refresh(vehiculo)
        return vehiculo
    
    def update(self, vehiculo_id: int, vehiculo_data: VehiculoUpdateDTO) -> Optional[Vehiculo]:
        vehiculo = self.get_by_id(vehiculo_id)
        if vehiculo:
            if vehiculo_data.modelo is not None:
                vehiculo.modelo = vehiculo_data.modelo
            if vehiculo_data.placa is not None:
                vehiculo.placa = vehiculo_data.placa
            self.db.commit()
            self.db.refresh(vehiculo)
        return vehiculo
    
    def delete(self, vehiculo_id: int) -> bool:
        vehiculo = self.get_by_id(vehiculo_id)
        if vehiculo:
            self.db.delete(vehiculo)
            self.db.commit()
            return True
        return False
