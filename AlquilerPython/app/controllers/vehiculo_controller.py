from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.vehiculo_service import VehiculoService
from app.dtos.vehiculo_dto import VehiculoCreateDTO, VehiculoUpdateDTO, VehiculoResponseDTO


router = APIRouter(prefix="/vehiculos", tags=["vehiculos"])


@router.get("/", response_model=List[VehiculoResponseDTO])
def get_all_vehiculos(db: Session = Depends(get_db)):
    """Obtener todos los vehículos"""
    service = VehiculoService(db)
    vehiculos = service.get_all()
    return vehiculos


@router.get("/{vehiculo_id}", response_model=VehiculoResponseDTO)
def get_vehiculo(vehiculo_id: int, db: Session = Depends(get_db)):
    """Obtener un vehículo por ID"""
    service = VehiculoService(db)
    vehiculo = service.get_by_id(vehiculo_id)
    if not vehiculo:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Vehículo no encontrado"
        )
    return vehiculo


@router.post("/", response_model=VehiculoResponseDTO, status_code=status.HTTP_201_CREATED)
def create_vehiculo(vehiculo_data: VehiculoCreateDTO, db: Session = Depends(get_db)):
    """Crear un nuevo vehículo"""
    service = VehiculoService(db)
    return service.create(vehiculo_data)


@router.put("/{vehiculo_id}", response_model=VehiculoResponseDTO)
def update_vehiculo(vehiculo_id: int, vehiculo_data: VehiculoUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar un vehículo"""
    service = VehiculoService(db)
    vehiculo = service.update(vehiculo_id, vehiculo_data)
    if not vehiculo:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Vehículo no encontrado"
        )
    return vehiculo


@router.delete("/{vehiculo_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_vehiculo(vehiculo_id: int, db: Session = Depends(get_db)):
    """Eliminar un vehículo"""
    service = VehiculoService(db)
    success = service.delete(vehiculo_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Vehículo no encontrado"
        )
