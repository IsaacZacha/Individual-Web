from sqlalchemy.future import select
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.models.usuario import Usuario
from app.core.security import verify_password

async def authenticate_user(db: AsyncSession, username: str, password: str):
    result = await db.execute(select(Usuario).where(Usuario.username == username))
    user = result.scalars().first()
    if user and verify_password(password, getattr(user, "contrasena_hash")):
        return user
    return None