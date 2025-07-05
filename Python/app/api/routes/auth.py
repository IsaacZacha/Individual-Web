from fastapi import Depends, HTTPException, status, APIRouter
from fastapi.security import OAuth2PasswordBearer
from jose import JWTError, jwt
from app.core.config import settings
from sqlalchemy.ext.asyncio import AsyncSession
from app.db.models.usuario import Usuario
from sqlalchemy.future import select
from app.services.auth import authenticate_user
from pydantic import BaseModel
from app.db.base import get_db

router = APIRouter()

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/auth/login")

class LoginRequest(BaseModel):
    username: str
    password: str

def credentials_exception():
    return HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="No se pudo validar credenciales",
        headers={"WWW-Authenticate": "Bearer"},
    )

@router.post("/login")
async def login(
    login_req: LoginRequest,
    db: AsyncSession = Depends(get_db)
):
    user = await authenticate_user(db, login_req.username, login_req.password)
    if not user:
        raise HTTPException(status_code=400, detail="Usuario o contraseña incorrectos")
    token_data = {"sub": user.username}
    access_token = jwt.encode(token_data, settings.SECRET_KEY, algorithm=settings.ALGORITHM)
    return {"access_token": access_token, "token_type": "bearer"}

async def get_current_user(
    token: str = Depends(oauth2_scheme),
    db: AsyncSession = Depends(get_db)
):
    try:
        payload = jwt.decode(token, settings.SECRET_KEY, algorithms=[settings.ALGORITHM])
        username = payload.get("sub")
        if not isinstance(username, str):
            raise credentials_exception()
    except JWTError:
        raise credentials_exception()

    result = await db.execute(select(Usuario).where(Usuario.username == username))
    user = result.scalars().first()
    if user is None:
        raise credentials_exception()
    return user