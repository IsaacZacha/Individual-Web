from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.inspeccion import Inspeccion
from app.dtos.inspeccion_dto import InspeccionCreateDTO, InspeccionUpdateDTO


class InspeccionService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Inspeccion]:
        return self.db.query(Inspeccion).all()
    
    def get_by_id(self, inspeccion_id: int) -> Optional[Inspeccion]:
        return self.db.query(Inspeccion).filter(Inspeccion.id_inspeccion == inspeccion_id).first()
    
    def create(self, inspeccion_data: InspeccionCreateDTO) -> Inspeccion:
        inspeccion = Inspeccion(
            alquiler_id=inspeccion_data.alquiler_id,
            fecha=inspeccion_data.fecha,
            observaciones=inspeccion_data.observaciones,
            estado_vehiculo=inspeccion_data.estado_vehiculo
        )
        self.db.add(inspeccion)
        self.db.commit()
        self.db.refresh(inspeccion)
        return inspeccion
    
    def update(self, inspeccion_id: int, inspeccion_data: InspeccionUpdateDTO) -> Optional[Inspeccion]:
        inspeccion = self.get_by_id(inspeccion_id)
        if inspeccion:
            if inspeccion_data.alquiler_id is not None:
                inspeccion.alquiler_id = inspeccion_data.alquiler_id
            if inspeccion_data.fecha is not None:
                inspeccion.fecha = inspeccion_data.fecha
            if inspeccion_data.observaciones is not None:
                inspeccion.observaciones = inspeccion_data.observaciones
            if inspeccion_data.estado_vehiculo is not None:
                inspeccion.estado_vehiculo = inspeccion_data.estado_vehiculo
            self.db.commit()
            self.db.refresh(inspeccion)
        return inspeccion
    
    def delete(self, inspeccion_id: int) -> bool:
        inspeccion = self.get_by_id(inspeccion_id)
        if inspeccion:
            self.db.delete(inspeccion)
            self.db.commit()
            return True
        return False
