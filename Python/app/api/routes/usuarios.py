from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.base import get_db
from app.schemas.mensaje import MensajeOut
from app.schemas.usuario import UsuarioCreate, UsuarioOut
from app.services.usuario import (
    get_usuarios,
    get_usuario_by_id,
    create_usuario,
    update_usuario,
    delete_usuario,
)
from app.api.routes.auth import get_current_user

router = APIRouter()
@router.get("/", response_model=list[UsuarioOut])
async def listar_usuarios(db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await get_usuarios(db)

@router.get("/{usuario_id}", response_model=UsuarioOut)
async def obtener_usuario(usuario_id: int, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    usuario = await get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario

@router.post("/", response_model=UsuarioOut)
async def crear_usuario(data: UsuarioCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await create_usuario(db, data)

@router.put("/{usuario_id}", response_model=UsuarioOut)
async def actualizar_usuario(usuario_id: int, data: UsuarioCreate, db: AsyncSession = Depends(get_db), user=Depends(get_current_user)):
    return await update_usuario(db, usuario_id, data)

@router.delete("/{usuario_id}", response_model=MensajeOut) 
async def eliminar_usuario(
    usuario_id: int,
    db: AsyncSession = Depends(get_db),
    user=Depends(get_current_user)
):
    return await delete_usuario(db, usuario_id)