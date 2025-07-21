from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.pago import Pago
from app.dtos.pago_dto import PagoCreateDTO, PagoUpdateDTO


class PagoService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Pago]:
        return self.db.query(Pago).all()
    
    def get_by_id(self, pago_id: int) -> Optional[Pago]:
        return self.db.query(Pago).filter(Pago.id_pago == pago_id).first()
    
    def create(self, pago_data: PagoCreateDTO) -> Pago:
        pago = Pago(
            alquiler_id=pago_data.alquiler_id,
            fecha=pago_data.fecha,
            monto=pago_data.monto,
            metodo=pago_data.metodo
        )
        self.db.add(pago)
        self.db.commit()
        self.db.refresh(pago)
        return pago
    
    def update(self, pago_id: int, pago_data: PagoUpdateDTO) -> Optional[Pago]:
        pago = self.get_by_id(pago_id)
        if pago:
            if pago_data.alquiler_id is not None:
                pago.alquiler_id = pago_data.alquiler_id
            if pago_data.fecha is not None:
                pago.fecha = pago_data.fecha
            if pago_data.monto is not None:
                pago.monto = pago_data.monto
            if pago_data.metodo is not None:
                pago.metodo = pago_data.metodo
            self.db.commit()
            self.db.refresh(pago)
        return pago
    
    def delete(self, pago_id: int) -> bool:
        pago = self.get_by_id(pago_id)
        if pago:
            self.db.delete(pago)
            self.db.commit()
            return True
        return False
