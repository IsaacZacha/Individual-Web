from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession, async_sessionmaker
from sqlalchemy.ext.declarative import declarative_base
from dotenv import load_dotenv
import os

load_dotenv()
DATABASE_URL = os.getenv("DATABASE_URL")
if not DATABASE_URL:
    raise ValueError("DATABASE_URL no está definida en el entorno")

engine = create_async_engine(DATABASE_URL, echo=False, connect_args={"ssl": True}) 
SessionLocal = async_sessionmaker(
    engine,  
    class_=AsyncSession,
    expire_on_commit=False
)
Base = declarative_base()

async def init_db():
    async with engine.begin() as conn:
        import app.db.models 
        await conn.run_sync(Base.metadata.create_all)

async def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        await db.close()