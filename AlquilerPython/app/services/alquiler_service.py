from sqlalchemy.orm import Session
from typing import List, Optional
from app.models import Cliente, Vehiculo, Reserva, Alquiler, Pago, Multa, Inspeccion

class ClienteService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Cliente]:
        return self.db.query(Cliente).all()
    
    def get_by_id(self, cliente_id: int) -> Optional[Cliente]:
        return self.db.query(Cliente).filter(Cliente.id == cliente_id).first()
    
    def create(self, cliente_data) -> Cliente:
        cliente = Cliente(
            nombre=cliente_data.nombre,
            email=cliente_data.email
        )
        self.db.add(cliente)
        self.db.commit()
        self.db.refresh(cliente)
        return cliente
    
    def update(self, cliente_id: int, cliente_data) -> Optional[Cliente]:
        cliente = self.get_by_id(cliente_id)
        if cliente:
            cliente.nombre = cliente_data.nombre
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

class VehiculoService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Vehiculo]:
        return self.db.query(Vehiculo).all()
    
    def get_by_id(self, vehiculo_id: int) -> Optional[Vehiculo]:
        return self.db.query(Vehiculo).filter(Vehiculo.id == vehiculo_id).first()
    
    def create(self, vehiculo_data) -> Vehiculo:
        vehiculo = Vehiculo(
            modelo=vehiculo_data.modelo,
            placa=vehiculo_data.placa
        )
        self.db.add(vehiculo)
        self.db.commit()
        self.db.refresh(vehiculo)
        return vehiculo
    
    def update(self, vehiculo_id: int, vehiculo_data) -> Optional[Vehiculo]:
        vehiculo = self.get_by_id(vehiculo_id)
        if vehiculo:
            vehiculo.modelo = vehiculo_data.modelo
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

class ReservaService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Reserva]:
        return self.db.query(Reserva).all()
    
    def get_by_id(self, reserva_id: int) -> Optional[Reserva]:
        return self.db.query(Reserva).filter(Reserva.id_reserva == reserva_id).first()
    
    def create(self, reserva_data) -> Reserva:
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
    
    def update(self, reserva_id: int, reserva_data) -> Optional[Reserva]:
        reserva = self.get_by_id(reserva_id)
        if reserva:
            reserva.cliente_id = reserva_data.cliente_id
            reserva.vehiculo_id = reserva_data.vehiculo_id
            reserva.fecha_reserva = reserva_data.fecha_reserva
            reserva.fecha_inicio = reserva_data.fecha_inicio
            reserva.fecha_fin = reserva_data.fecha_fin
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

class AlquilerService:
    def __init__(self, db: Session):
        self.db = db
    
    def get_all(self) -> List[Alquiler]:
        return self.db.query(Alquiler).all()
    
    def get_by_id(self, alquiler_id: int) -> Optional[Alquiler]:
        return self.db.query(Alquiler).filter(Alquiler.id_alquiler == alquiler_id).first()
    
    def create(self, alquiler_data) -> Alquiler:
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
