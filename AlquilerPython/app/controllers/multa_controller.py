from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.multa_service import MultaService
from app.dtos.multa_dto import MultaCreateDTO, MultaUpdateDTO, MultaResponseDTO


router = APIRouter(prefix="/multas", tags=["multas"])


@router.get("/", response_model=List[MultaResponseDTO])
def get_all_multas(db: Session = Depends(get_db)):
    """Obtener todas las multas"""
    service = MultaService(db)
    multas = service.get_all()
    return multas


@router.get("/{multa_id}", response_model=MultaResponseDTO)
def get_multa(multa_id: int, db: Session = Depends(get_db)):
    """Obtener una multa por ID"""
    service = MultaService(db)
    multa = service.get_by_id(multa_id)
    if not multa:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Multa no encontrada"
        )
    return multa


@router.post("/", response_model=MultaResponseDTO, status_code=status.HTTP_201_CREATED)
def create_multa(multa_data: MultaCreateDTO, db: Session = Depends(get_db)):
    """Crear una nueva multa"""
    service = MultaService(db)
    return service.create(multa_data)


@router.put("/{multa_id}", response_model=MultaResponseDTO)
def update_multa(multa_id: int, multa_data: MultaUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar una multa"""
    service = MultaService(db)
    multa = service.update(multa_id, multa_data)
    if not multa:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Multa no encontrada"
        )
    return multa


@router.delete("/{multa_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_multa(multa_id: int, db: Session = Depends(get_db)):
    """Eliminar una multa"""
    service = MultaService(db)
    success = service.delete(multa_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Multa no encontrada"
        )
