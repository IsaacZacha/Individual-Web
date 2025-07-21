from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.alquiler import Alquiler
from app.dtos.alquiler_dto import AlquilerCreateDTO, AlquilerUpdateDTO


class AlquilerService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Alquiler]:
        return self.db.query(Alquiler).all()
    
    def get_by_id(self, alquiler_id: int) -> Optional[Alquiler]:
        return self.db.query(Alquiler).filter(Alquiler.id_alquiler == alquiler_id).first()
    
    def create(self, alquiler_data: AlquilerCreateDTO) -> Alquiler:
        alquiler = Alquiler(
            reserva_id=alquiler_data.reserva_id,
            fecha_entrega=alquiler_data.fecha_entrega,
            fecha_devolucion=alquiler_data.fecha_devolucion,
            kilometraje_inicial=alquiler_data.kilometraje_inicial,
            kilometraje_final=alquiler_data.kilometraje_final,
            total=alquiler_data.total
        )
        self.db.add(alquiler)
        self.db.commit()
        self.db.refresh(alquiler)
        return alquiler

    def update(self, alquiler_id: int, alquiler_data: AlquilerUpdateDTO) -> Optional[Alquiler]:
        alquiler = self.get_by_id(alquiler_id)
        if alquiler:
            if alquiler_data.reserva_id is not None:
                alquiler.reserva_id = alquiler_data.reserva_id
            if alquiler_data.fecha_entrega is not None:
                alquiler.fecha_entrega = alquiler_data.fecha_entrega
            if alquiler_data.fecha_devolucion is not None:
                alquiler.fecha_devolucion = alquiler_data.fecha_devolucion
            if alquiler_data.kilometraje_inicial is not None:
                alquiler.kilometraje_inicial = alquiler_data.kilometraje_inicial
            if alquiler_data.kilometraje_final is not None:
                alquiler.kilometraje_final = alquiler_data.kilometraje_final
            if alquiler_data.total is not None:
                alquiler.total = alquiler_data.total
            self.db.commit()
            self.db.refresh(alquiler)
        return alquiler

    def delete(self, alquiler_id: int) -> bool:
        alquiler = self.get_by_id(alquiler_id)
        if alquiler:
            self.db.delete(alquiler)
            self.db.commit()
            return True
        return False
