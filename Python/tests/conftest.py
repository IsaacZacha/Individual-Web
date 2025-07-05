import os
import pytest
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession, async_sessionmaker
from app.db.base import Base, get_db
from app.main import app

TEST_DATABASE_URL = "sqlite+aiosqlite:///./test_db.sqlite"

try:
    os.remove("test_db.sqlite")
except PermissionError:
    print("⚠️  test_db.sqlite está en uso, no se pudo borrar.")
except FileNotFoundError:
    pass

@pytest.fixture(scope="session")
def test_engine():
    return create_async_engine(TEST_DATABASE_URL, future=True)

@pytest.fixture(scope="session", autouse=True)
def create_tables(test_engine):
    import asyncio
    async def _create():
        async with test_engine.begin() as conn:
            await conn.run_sync(Base.metadata.create_all)
    asyncio.run(_create())
    yield

@pytest.fixture(scope="session")
def async_session_factory(test_engine, create_tables):
    return async_sessionmaker(
        test_engine, class_=AsyncSession, expire_on_commit=False
    )

@pytest.fixture(autouse=True)
def override_get_db(async_session_factory):
    async def _override_get_db():
        async with async_session_factory() as session:
            yield session
    app.dependency_overrides[get_db] = _override_get_db

@pytest.fixture(autouse=True)
def override_get_current_user():
    def fake_user():
        return {"username": "testuser"}
    from app.api.routes import empleados, usuarios, roles, sucursales, vehiculos_sucursal
    app.dependency_overrides[empleados.get_current_user] = fake_user
    app.dependency_overrides[usuarios.get_current_user] = fake_user
    app.dependency_overrides[roles.get_current_user] = fake_user
    app.dependency_overrides[sucursales.get_current_user] = fake_user
    app.dependency_overrides[vehiculos_sucursal.get_current_user] = fake_user
    yield
    app.dependency_overrides = {}
    
from app.db.models.vehiculo import Vehiculo

@pytest.fixture(scope="session")
async def vehiculo_de_prueba(async_session_factory):
    async with async_session_factory() as session:
        vehiculo = Vehiculo(
            placa="test-123",
            marca="Toyota",
            modelo="Hylux",
            anio=2020,
            tipo_id="sedan",
            estado="disponible"
        )
        session.add(vehiculo)
        await session.commit()
        await session.refresh(vehiculo)
        return vehiculo.id_vehiculo