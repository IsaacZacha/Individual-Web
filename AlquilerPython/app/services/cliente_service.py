from sqlalchemy.orm import Session
from typing import List, Optional
from app.models.cliente import Cliente
from app.dtos.cliente_dto import ClienteCreateDTO, ClienteUpdateDTO


class ClienteService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Cliente]:
        return self.db.query(Cliente).all()
    
    def get_by_id(self, cliente_id: int) -> Optional[Cliente]:
        return self.db.query(Cliente).filter(Cliente.id == cliente_id).first()
    
    def create(self, cliente_data: ClienteCreateDTO) -> Cliente:
        cliente = Cliente(
            nombre=cliente_data.nombre,
            email=cliente_data.email
        )
        self.db.add(cliente)
        self.db.commit()
        self.db.refresh(cliente)
        return cliente
    
    def update(self, cliente_id: int, cliente_data: ClienteUpdateDTO) -> Optional[Cliente]:
        cliente = self.get_by_id(cliente_id)
        if cliente:
            if cliente_data.nombre is not None:
                cliente.nombre = cliente_data.nombre
            if cliente_data.email is not None:
                cliente.email = cliente_data.email
            self.db.commit()
            self.db.refresh(cliente)
        return cliente
    
    def delete(self, cliente_id: int) -> bool:
        cliente = self.get_by_id(cliente_id)
        if cliente:
            self.db.delete(cliente)
            self.db.commit()
            return True
        return False
