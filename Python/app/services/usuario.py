from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy.future import select
from fastapi import HTTPException
from app.db.models.usuario import Usuario
from app.db.models.empleado import Empleado
from app.db.models.rol import Rol
from app.schemas.usuario import UsuarioCreate
from app.core.security import hash_password
from sqlalchemy.exc import IntegrityError
from typing import Optional, Sequence

async def get_usuarios(db: AsyncSession) -> Sequence[Usuario]:
    return (await db.execute(select(Usuario))).scalars().all()

async def get_usuario_by_id(db: AsyncSession, usuario_id: int) -> Optional[Usuario]:
    return (await db.execute(select(Usuario).where(Usuario.id_usuario == usuario_id))).scalars().first()

async def _validar_fk(db: AsyncSession, empleado_id: int, rol_id: int):
    if not (await db.execute(select(Empleado).where(Empleado.id_empleado == empleado_id))).scalars().first():
        raise HTTPException(status_code=400, detail="El empleado no existe")
    if not (await db.execute(select(Rol).where(Rol.id_rol == rol_id))).scalars().first():
        raise HTTPException(status_code=400, detail="El rol no existe")

async def create_usuario(db: AsyncSession, data: UsuarioCreate) -> Usuario:
    await _validar_fk(db, data.empleado_id, data.rol_id)
    usuario = Usuario(
        username=data.username,
        contrasena_hash=hash_password(data.contrasena),
        empleado_id=data.empleado_id,
        rol_id=data.rol_id
    )
    db.add(usuario)
    try:
        await db.commit()
        await db.refresh(usuario)
    except IntegrityError:
        await db.rollback()
        raise HTTPException(status_code=400, detail="El nombre de usuario ya existe. Por favor, elige otro.")
    return usuario

async def update_usuario(db: AsyncSession, usuario_id: int, data: UsuarioCreate) -> Usuario:
    usuario = await get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    await _validar_fk(db, data.empleado_id, data.rol_id)
    existe = (await db.execute(
        select(Usuario).where(Usuario.username == data.username, Usuario.id_usuario != usuario_id)
    )).scalars().first()
    if existe:
        raise HTTPException(status_code=400, detail="El nombre de usuario ya existe. Por favor, elige otro.")
    usuario.username = data.username # type: ignore
    usuario.contrasena_hash = hash_password(data.contrasena) # type: ignore
    usuario.empleado_id = data.empleado_id # type: ignore
    usuario.rol_id = data.rol_id # type: ignore
    await db.commit()
    await db.refresh(usuario)
    return usuario

async def delete_usuario(db: AsyncSession, usuario_id: int) -> dict:
    usuario = await get_usuario_by_id(db, usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    await db.delete(usuario)
    await db.commit()
    return {"mensaje": f"Usuario '{usuario.username}' eliminado correctamente"}