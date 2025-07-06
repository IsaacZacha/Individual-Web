from sqlalchemy import create_engine, MetaData
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from databases import Database
from app.config import Settings

settings = Settings()

# Configurar URL para asyncpg
ASYNC_DATABASE_URL = settings.database_url.replace("postgresql://", "postgresql+asyncpg://")

# SQLAlchemy setup
engine = create_engine(settings.database_url)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Databases setup for async operations con asyncpg
database = Database(ASYNC_DATABASE_URL)
metadata = MetaData()

# Dependency para obtener la sesión de base de datos
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Función para inicializar la base de datos
async def init_db():
    await database.connect()

# Función para cerrar la conexión a la base de datos
async def close_db():
    await database.disconnect()
