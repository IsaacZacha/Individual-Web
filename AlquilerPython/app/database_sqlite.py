from sqlalchemy import create_engine, MetaData
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from databases import Database
from app.config_sqlite import settings

# SQLAlchemy setup para SQLite
engine = create_engine(
    settings.database_url,
    connect_args={"check_same_thread": False}  # Solo para SQLite
)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Databases setup for async operations
database = Database(settings.database_url)
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
    # Crear las tablas si no existen
    Base.metadata.create_all(bind=engine)

# Función para cerrar la conexión a la base de datos
async def close_db():
    await database.disconnect()
