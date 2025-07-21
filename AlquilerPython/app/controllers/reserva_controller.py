from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.reserva_service import ReservaService
from app.dtos.reserva_dto import ReservaCreateDTO, ReservaUpdateDTO, ReservaResponseDTO


router = APIRouter(prefix="/reservas", tags=["reservas"])


@router.get("/", response_model=List[ReservaResponseDTO])
def get_all_reservas(db: Session = Depends(get_db)):
    """Obtener todas las reservas"""
    service = ReservaService(db)
    reservas = service.get_all()
    return reservas


@router.get("/{reserva_id}", response_model=ReservaResponseDTO)
def get_reserva(reserva_id: int, db: Session = Depends(get_db)):
    """Obtener una reserva por ID"""
    service = ReservaService(db)
    reserva = service.get_by_id(reserva_id)
    if not reserva:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reserva no encontrada"
        )
    return reserva


@router.post("/", response_model=ReservaResponseDTO, status_code=status.HTTP_201_CREATED)
def create_reserva(reserva_data: ReservaCreateDTO, db: Session = Depends(get_db)):
    """Crear una nueva reserva"""
    service = ReservaService(db)
    return service.create(reserva_data)


@router.put("/{reserva_id}", response_model=ReservaResponseDTO)
def update_reserva(reserva_id: int, reserva_data: ReservaUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar una reserva"""
    service = ReservaService(db)
    reserva = service.update(reserva_id, reserva_data)
    if not reserva:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reserva no encontrada"
        )
    return reserva


@router.delete("/{reserva_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_reserva(reserva_id: int, db: Session = Depends(get_db)):
    """Eliminar una reserva"""
    service = ReservaService(db)
    success = service.delete(reserva_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reserva no encontrada"
        )
