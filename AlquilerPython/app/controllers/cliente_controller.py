from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.services.cliente_service import ClienteService
from app.dtos.cliente_dto import ClienteCreateDTO, ClienteUpdateDTO, ClienteResponseDTO


router = APIRouter(prefix="/clientes", tags=["clientes"])


@router.get("/", response_model=List[ClienteResponseDTO])
def get_all_clientes(db: Session = Depends(get_db)):
    """Obtener todos los clientes"""
    service = ClienteService(db)
    clientes = service.get_all()
    return clientes


@router.get("/{cliente_id}", response_model=ClienteResponseDTO)
def get_cliente(cliente_id: int, db: Session = Depends(get_db)):
    """Obtener un cliente por ID"""
    service = ClienteService(db)
    cliente = service.get_by_id(cliente_id)
    if not cliente:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Cliente no encontrado"
        )
    return cliente


@router.post("/", response_model=ClienteResponseDTO, status_code=status.HTTP_201_CREATED)
def create_cliente(cliente_data: ClienteCreateDTO, db: Session = Depends(get_db)):
    """Crear un nuevo cliente"""
    service = ClienteService(db)
    return service.create(cliente_data)


@router.put("/{cliente_id}", response_model=ClienteResponseDTO)
def update_cliente(cliente_id: int, cliente_data: ClienteUpdateDTO, db: Session = Depends(get_db)):
    """Actualizar un cliente"""
    service = ClienteService(db)
    cliente = service.update(cliente_id, cliente_data)
    if not cliente:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Cliente no encontrado"
        )
    return cliente


@router.delete("/{cliente_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_cliente(cliente_id: int, db: Session = Depends(get_db)):
    """Eliminar un cliente"""
    service = ClienteService(db)
    success = service.delete(cliente_id)
    if not success:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Cliente no encontrado"
        )
