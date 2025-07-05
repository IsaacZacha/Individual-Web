import asyncio
from app.db.base import SessionLocal, init_db
from app.core.security import hash_password
from app.db.models.usuario import Usuario
from app.db.models.rol import Rol
from app.db.models.empleado import Empleado

async def crear_usuario_inicial():
    await init_db()
    async with SessionLocal() as session:
        # Crear rol admin si no existe
        rol_admin = Rol(nombre="admin")
        session.add(rol_admin)
        await session.flush()

        # Crear empleado
        empleado = Empleado(nombre="Admin", cargo="Administrador", correo="admin@demo.com", telefono="0999999999")
        session.add(empleado)
        await session.flush()

        # Crear usuario
        user = Usuario(
            username="admin",
            contrasena_hash=hash_password("admin123"),
            rol_id=rol_admin.id_rol,
            empleado_id=empleado.id_empleado
        )
        session.add(user)
        await session.commit()
        print("Usuario admin creado: admin / admin123")

asyncio.run(crear_usuario_inicial())
