from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.multa import Multa
from app.dtos.multa_dto import MultaCreateDTO, MultaUpdateDTO


class MultaService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Multa]:
        return self.db.query(Multa).all()
    
    def get_by_id(self, multa_id: int) -> Optional[Multa]:
        return self.db.query(Multa).filter(Multa.id_multa == multa_id).first()
    
    def create(self, multa_data: MultaCreateDTO) -> Multa:
        multa = Multa(
            alquiler_id=multa_data.alquiler_id,
            motivo=multa_data.motivo,
            monto=multa_data.monto,
            fecha=multa_data.fecha
        )
        self.db.add(multa)
        self.db.commit()
        self.db.refresh(multa)
        return multa
    
    def update(self, multa_id: int, multa_data: MultaUpdateDTO) -> Optional[Multa]:
        multa = self.get_by_id(multa_id)
        if multa:
            if multa_data.alquiler_id is not None:
                multa.alquiler_id = multa_data.alquiler_id
            if multa_data.motivo is not None:
                multa.motivo = multa_data.motivo
            if multa_data.monto is not None:
                multa.monto = multa_data.monto
            if multa_data.fecha is not None:
                multa.fecha = multa_data.fecha
            self.db.commit()
            self.db.refresh(multa)
        return multa
    
    def delete(self, multa_id: int) -> bool:
        multa = self.get_by_id(multa_id)
        if multa:
            self.db.delete(multa)
            self.db.commit()
            return True
        return False
