from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.alquiler_service import AlquilerService
from app.dtos.alquiler_dto import AlquilerCreateDTO, AlquilerUpdateDTO, AlquilerResponseDTO


router = APIRouter(prefix="/alquileres", tags=["alquileres"])


@router.get("/", response_model=List[AlquilerResponseDTO])
def get_all_alquileres(db: Session = Depends(get_db)):
    """Obtener todos los alquileres"""
    service = AlquilerService(db)
    alquileres = service.get_all()
    return alquileres


@router.get("/{alquiler_id}", response_model=AlquilerResponseDTO)
def get_alquiler(alquiler_id: int, db: Session = Depends(get_db)):
    """Obtener un alquiler por ID"""
    service = AlquilerService(db)
    alquiler = service.get_by_id(alquiler_id)
    if not alquiler:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Alquiler no encontrado"
        )
    return alquiler


@router.post("/", response_model=AlquilerResponseDTO, status_code=status.HTTP_201_CREATED)
def create_alquiler(alquiler_data: AlquilerCreateDTO, db: Session = Depends(get_db)):
    """Crear un nuevo alquiler"""
    service = AlquilerService(db)
    return service.create(alquiler_data)


@router.put("/{alquiler_id}", response_model=AlquilerResponseDTO)
def update_alquiler(alquiler_id: int, alquiler_data: AlquilerUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar un alquiler"""
    service = AlquilerService(db)
    alquiler = service.update(alquiler_id, alquiler_data)
    if not alquiler:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Alquiler no encontrado"
        )
    return alquiler


@router.delete("/{alquiler_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_alquiler(alquiler_id: int, db: Session = Depends(get_db)):
    """Eliminar un alquiler"""
    service = AlquilerService(db)
    success = service.delete(alquiler_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Alquiler no encontrado"
        )
