from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.inspeccion_service import InspeccionService
from app.dtos.inspeccion_dto import InspeccionCreateDTO, InspeccionUpdateDTO, InspeccionResponseDTO


router = APIRouter(prefix="/inspecciones", tags=["inspecciones"])


@router.get("/", response_model=List[InspeccionResponseDTO])
def get_all_inspecciones(db: Session = Depends(get_db)):
    """Obtener todas las inspecciones"""
    service = InspeccionService(db)
    inspecciones = service.get_all()
    return inspecciones


@router.get("/{inspeccion_id}", response_model=InspeccionResponseDTO)
def get_inspeccion(inspeccion_id: int, db: Session = Depends(get_db)):
    """Obtener una inspección por ID"""
    service = InspeccionService(db)
    inspeccion = service.get_by_id(inspeccion_id)
    if not inspeccion:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Inspección no encontrada"
        )
    return inspeccion


@router.post("/", response_model=InspeccionResponseDTO, status_code=status.HTTP_201_CREATED)
def create_inspeccion(inspeccion_data: InspeccionCreateDTO, db: Session = Depends(get_db)):
    """Crear una nueva inspección"""
    service = InspeccionService(db)
    return service.create(inspeccion_data)


@router.put("/{inspeccion_id}", response_model=InspeccionResponseDTO)
def update_inspeccion(inspeccion_id: int, inspeccion_data: InspeccionUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar una inspección"""
    service = InspeccionService(db)
    inspeccion = service.update(inspeccion_id, inspeccion_data)
    if not inspeccion:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Inspección no encontrada"
        )
    return inspeccion


@router.delete("/{inspeccion_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_inspeccion(inspeccion_id: int, db: Session = Depends(get_db)):
    """Eliminar una inspección"""
    service = InspeccionService(db)
    success = service.delete(inspeccion_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Inspección no encontrada"
        )
