from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.reserva import Reserva
from app.dtos.reserva_dto import ReservaCreateDTO, ReservaUpdateDTO


class ReservaService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Reserva]:
        return self.db.query(Reserva).all()
    
    def get_by_id(self, reserva_id: int) -> Optional[Reserva]:
        return self.db.query(Reserva).filter(Reserva.id_reserva == reserva_id).first()
    
    def create(self, reserva_data: ReservaCreateDTO) -> Reserva:
        reserva = Reserva(
            cliente_id=reserva_data.cliente_id,
            vehiculo_id=reserva_data.vehiculo_id,
            fecha_reserva=reserva_data.fecha_reserva,
            fecha_inicio=reserva_data.fecha_inicio,
            fecha_fin=reserva_data.fecha_fin,
            estado=reserva_data.estado
        )
        self.db.add(reserva)
        self.db.commit()
        self.db.refresh(reserva)
        return reserva
    
    def update(self, reserva_id: int, reserva_data: ReservaUpdateDTO) -> Optional[Reserva]:
        reserva = self.get_by_id(reserva_id)
        if reserva:
            if reserva_data.cliente_id is not None:
                reserva.cliente_id = reserva_data.cliente_id
            if reserva_data.vehiculo_id is not None:
                reserva.vehiculo_id = reserva_data.vehiculo_id
            if reserva_data.fecha_reserva is not None:
                reserva.fecha_reserva = reserva_data.fecha_reserva
            if reserva_data.fecha_inicio is not None:
                reserva.fecha_inicio = reserva_data.fecha_inicio
            if reserva_data.fecha_fin is not None:
                reserva.fecha_fin = reserva_data.fecha_fin
            if reserva_data.estado is not None:
                reserva.estado = reserva_data.estado
            self.db.commit()
            self.db.refresh(reserva)
        return reserva
    
    def delete(self, reserva_id: int) -> bool:
        reserva = self.get_by_id(reserva_id)
        if reserva:
            self.db.delete(reserva)
            self.db.commit()
            return True
        return False
