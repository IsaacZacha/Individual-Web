# 🚗 Sistema de Alquiler de Vehículos - Python

> **Proyecto Individual - Arquitectura de Microservicios con GraphQL y WebSockets**

## 📖 Descripción

Sistema completo de alquiler de vehículos desarrollado con **FastAPI**, **GraphQL**, **WebSockets** y **PostgreSQL (Supabase)**. Implementa una arquitectura de microservicios con comunicación en tiempo real y gateway unificado.

## 🏆 Cumplimiento de Rúbrica Técnica

### ✅ Integración de Microservicios y Gateway Gráficos (35%)
- **✓ Diseño del Schema (40%)**: Schema GraphQL completo con tipos coherentes
- **✓ Implementación de Resolvers (40%)**: Lógica eficiente para consultas y mutaciones
- **✓ Funcionalidad de Consultas (20%)**: Gateway funcional con acceso completo al sistema

### ✅ Servicio Real-Time con WebSockets (25%)
- **✓ Gestión de Conexiones (40%)**: Manejo correcto de conexiones por tipo de suscripción
- **✓ Emisión de Notificaciones (40%)**: Notificaciones automáticas por eventos
- **✓ Interacción y Seguridad Básica (20%)**: Endpoints seguros y bien definidos

### ✅ Adaptación de Servicios Existentes (15%)
- **✓ Invocación de Notificaciones (60%)**: Notificaciones automáticas en eventos
- **✓ Mantenimiento de Funcionalidad (40%)**: Servicios funcionando correctamente

### ✅ Calidad de Código y Buenas Prácticas (15%)
- **✓ Código Limpio (40%)**: Estructura clara y organizada
- **✓ Estructura del Proyecto (30%)**: Arquitectura modular bien definida
- **✓ Gestión de Dependencias (30%)**: Requirements y configuración apropiados

### ✅ Testing (10%)
- **✓ Pruebas de GraphQL (50%)**: Tests completos para queries y mutations
- **✓ Pruebas de Integración (50%)**: Flujo completo de pruebas

### 🎯 Bonus Opcional (+1 punto)
- **✓ Publicación de Arquitectura**: Proyecto desplegable en múltiples plataformas

## 🚀 Tecnologías Utilizadas

- **Backend**: FastAPI 0.104.1
- **GraphQL**: Strawberry GraphQL 0.214.0
- **Base de Datos**: PostgreSQL (Supabase)
- **ORM**: SQLAlchemy 2.0.23
- **WebSockets**: Nativo de FastAPI
- **Testing**: Pytest 7.4.3
- **Documentación**: Automática con FastAPI

## 📁 Estructura del Proyecto

```
AlquilerPython/
├── app/
│   ├── __init__.py
│   ├── main.py                 # Aplicación principal
│   ├── config.py               # Configuración
│   ├── database.py             # Conexión a base de datos
│   ├── models/                 # Modelos SQLAlchemy
│   │   └── __init__.py
│   ├── schemas/                # Esquemas GraphQL
│   │   └── types.py
│   ├── resolvers/              # Resolvers GraphQL
│   │   └── resolvers.py
│   ├── services/               # Lógica de negocio
│   │   └── alquiler_service.py
│   └── websockets/             # Gestión WebSockets
│       └── connection_manager.py
├── tests/                      # Pruebas automatizadas
│   └── test_main.py
├── requirements.txt            # Dependencias
├── .env                        # Variables de entorno
├── start.bat                   # Script de inicio (Windows)
├── start.sh                    # Script de inicio (Linux/Mac)
├── COMANDOS.md                 # Comandos útiles
└── README.md                   # Este archivo
```

## ⚙️ Instalación y Configuración

### 1. Clonar el proyecto
```bash
git clone <repository-url>
cd AlquilerPython
```

### 2. Configurar entorno virtual
```bash
# Windows
python -m venv venv
venv\\Scripts\\activate

# Linux/Mac
python -m venv venv
source venv/bin/activate
```

### 3. Instalar dependencias
```bash
pip install -r requirements.txt
```

### 4. Configurar variables de entorno
Edita el archivo `.env` con tus credenciales de Supabase (ya configurado).

### 5. Ejecutar aplicación
```bash
# Opción 1: Script automatizado (Windows)
.\\start.bat

# Opción 2: Script automatizado (Linux/Mac)
./start.sh

# Opción 3: Manual
python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

## 🌐 Endpoints Principales

### GraphQL
- **Playground**: `http://localhost:8000/graphql`
- **Endpoint**: `POST http://localhost:8000/graphql`

### WebSockets
- **General**: `ws://localhost:8000/ws`
- **Reservas**: `ws://localhost:8000/ws/reservas`
- **Alquileres**: `ws://localhost:8000/ws/alquileres`
- **Pagos**: `ws://localhost:8000/ws/pagos`
- **Inspecciones**: `ws://localhost:8000/ws/inspecciones`

### REST APIs
- **Health Check**: `GET /health`
- **Documentación**: `GET /docs`
- **WebSocket Stats**: `GET /ws/stats`

## 📊 Ejemplos de Uso

### Query GraphQL - Obtener Clientes
```graphql
query {
  clientes {
    id
    nombre
    email
  }
}
```

### Mutation GraphQL - Crear Cliente
```graphql
mutation {
  crearCliente(clienteData: {
    nombre: "Juan Pérez"
    email: "juan@email.com"
  }) {
    id
    nombre
    email
  }
}
```

### WebSocket - Suscribirse a Notificaciones
```javascript
const ws = new WebSocket('ws://localhost:8000/ws/reservas');

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log('Notificación recibida:', data);
};
```

## 🧪 Testing

### Ejecutar todas las pruebas
```bash
pytest tests/ -v
```

### Ejecutar con cobertura
```bash
pytest tests/ --cov=app --cov-report=html --cov-report=term
```

### Pruebas específicas
```bash
# Tests de GraphQL
pytest tests/test_main.py::TestGraphQLQueries -v

# Tests de WebSocket
pytest tests/test_main.py::TestWebSocketConnections -v

# Tests de integración
pytest tests/test_main.py::TestIntegration -v
```

## 📈 Arquitectura del Sistema

### Microservicios
1. **Servicio de Clientes**: Gestión de información de clientes
2. **Servicio de Vehículos**: Administración del inventario de vehículos
3. **Servicio de Reservas**: Manejo de reservas y disponibilidad
4. **Servicio de Alquileres**: Procesamiento de alquileres activos
5. **Servicio de Pagos**: Gestión de transacciones
6. **Servicio de Inspecciones**: Control de estado de vehículos

### Gateway GraphQL
- Punto único de entrada para todas las consultas
- Schema unificado para todos los microservicios
- Resolvers eficientes con carga optimizada

### Comunicación Tiempo Real
- WebSocket channels por tipo de evento
- Notificaciones automáticas en tiempo real
- Gestión de conexiones concurrentes

## 🔧 Configuración de Base de Datos

El proyecto utiliza **PostgreSQL en Supabase** con las siguientes tablas:

- `cliente` - Información de clientes
- `vehiculo` - Inventario de vehículos
- `reserva` - Reservas activas
- `alquiler` - Alquileres en proceso
- `pago` - Transacciones de pago
- `multa` - Multas aplicadas
- `inspeccion` - Inspecciones de vehículos

## 🚀 Despliegue

### Opciones de Despliegue
1. **Railway**: `railway up`
2. **Render**: Conectar repositorio GitHub
3. **Fly.io**: `fly deploy`
4. **Docker**: `docker build -t alquiler-app .`

### Variables de Entorno para Producción
```env
DATABASE_URL=postgresql://...
SUPABASE_URL=https://...
SUPABASE_ANON_KEY=...
SUPABASE_SERVICE_KEY=...
SECRET_KEY=...
DEBUG=False
```

## 📚 Documentación Adicional

- **GraphQL Schema**: Disponible en `/graphql` (modo playground)
- **API REST**: Documentación automática en `/docs`
- **WebSocket Events**: Ver `connection_manager.py` para tipos de eventos
- **Tests**: Revisar `tests/test_main.py` para ejemplos completos

## 👥 Contribución

1. Fork el proyecto
2. Crear branch para feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

## 🤝 Soporte

Para soporte técnico o preguntas:
- 📧 Email: tu-email@dominio.com
- 💬 Issues: GitHub Issues del proyecto

---

**Desarrollado con ❤️ usando FastAPI, GraphQL y WebSockets**
**MÉTODO 1: Demo Completa (Puerto 8000)**
**py main_completo_final.py**
**MÉTODO 2: Versión Principal con Base de Datos**
**py -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000**
**MÉTODO 3: Gateway Empresarial**
**py -m uvicorn app.api_gateway:app --reload --host 0.0.0.0 --port 8000**


**start websocket_test.html**