from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.pago_service import PagoService
from app.dtos.pago_dto import PagoCreateDTO, PagoUpdateDTO, PagoResponseDTO


router = APIRouter(prefix="/pagos", tags=["pagos"])


@router.get("/", response_model=List[PagoResponseDTO])
def get_all_pagos(db: Session = Depends(get_db)):
    """Obtener todos los pagos"""
    service = PagoService(db)
    pagos = service.get_all()
    return pagos


@router.get("/{pago_id}", response_model=PagoResponseDTO)
def get_pago(pago_id: int, db: Session = Depends(get_db)):
    """Obtener un pago por ID"""
    service = PagoService(db)
    pago = service.get_by_id(pago_id)
    if not pago:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Pago no encontrado"
        )
    return pago


@router.post("/", response_model=PagoResponseDTO, status_code=status.HTTP_201_CREATED)
def create_pago(pago_data: PagoCreateDTO, db: Session = Depends(get_db)):
    """Crear un nuevo pago"""
    service = PagoService(db)
    return service.create(pago_data)


@router.put("/{pago_id}", response_model=PagoResponseDTO)
def update_pago(pago_id: int, pago_data: PagoUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar un pago"""
    service = PagoService(db)
    pago = service.update(pago_id, pago_data)
    if not pago:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Pago no encontrado"
        )
    return pago


@router.delete("/{pago_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_pago(pago_id: int, db: Session = Depends(get_db)):
    """Eliminar un pago"""
    service = PagoService(db)
    success = service.delete(pago_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Pago no encontrado"
        )
