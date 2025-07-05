# Sistema de Administración de Vehículos y Empleados

## Proyecto Final - Microservicios con Laravel 12

Este proyecto implementa una **arquitectura completa en PHP** usando **Laravel 12** con:

- **GraphQL Gateway** (Lighthouse PHP) como punto único de entrada
- **WebSockets en tiempo real** (Laravel Reverb nativo)
- **Dashboard interactivo** con gráficos en tiempo real
- **Arquitectura de microservicios** completamente en PHP
- **Sistema de eventos** para actualizaciones automáticas

## Características Implementadas

### GraphQL Gateway (Lighthouse PHP)

- **Endpoint único**: `/graphql` para todas las operaciones
- **Schema completo** con tipos para Empleados, Vehículos, Sucursales, Usuarios y Asignaciones
- **Consultas optimizadas** con relaciones Eloquent
- **Validación automática** de datos de entrada
- **GraphiQL Playground** disponible en `/graphiql`

### WebSockets en Tiempo Real (Laravel Reverb)

- **Servidor WebSocket nativo** de Laravel en puerto 8080
- **Eventos automáticos** para operaciones CRUD
- **Canales específicos** por tipo de entidad
- **Reconexión automática** con manejo de errores
- **Broadcasting** configurado con Pusher Protocol

### Dashboard Interactivo

- **Tabla principal**: Asignaciones Vehículo-Sucursal
- **Gráficos en tiempo real** con Chart.js
- **Estadísticas actualizadas** cada 15 segundos
- **Feed de actividad** en tiempo real
- **Interfaz responsive** con Bootstrap 5
- **Navegación suave** entre secciones

## Arquitectura del Sistema

```text
┌─────────────────────────────────────────────────────────────┐
│                     CLIENTE (Navegador)                    │
│                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │   Dashboard     │  │   GraphQL       │  │  WebSocket  │ │
│  │   Principal     │  │   Queries       │  │   Cliente   │ │
│  │  (Bootstrap)    │  │   (Fetch API)   │  │  (Pusher)   │ │
│  └─────────────────┘  └─────────────────┘  └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                 SERVIDOR LARAVEL 12                        │
│                     Puerto: 8000                           │
│                                                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │   GraphQL       │  │   WebSocket     │  │  Dashboard  │ │
│  │   Lighthouse    │  │   Reverb        │  │   Routes    │ │
│  │   /graphql      │  │   Puerto: 8080  │  │   Blade     │ │
│  └─────────────────┘  └─────────────────┘  └─────────────┘ │
│                                                             │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                    MODELOS ELOQUENT                    │ │
│  │  Empleado • Vehiculo • Sucursal • User • VehiculoSucursal │
│  └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                 BASE DE DATOS POSTGRESQL                   │
│                                                             │
│  empleado • vehiculo • sucursal • users • vehiculo_sucursal │
└─────────────────────────────────────────────────────────────┘
```

## Instalación y Uso

### Opción 1: Inicio Completo (Recomendado)

```bash
start-servers.bat
```

### Opción 2: Servicios Individuales

**Terminal 1 - WebSocket Server:**

```bash
php artisan reverb:start
```

**Terminal 2 - Laravel Server:**

```bash
php artisan serve
```

**Nota**: No se requiere Queue Worker ya que usa `QUEUE_CONNECTION=sync` (procesamiento inmediato)

## URLs de Acceso

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **Dashboard Principal** | <http://127.0.0.1:8000/dashboard> | Dashboard completo funcional (redirige a v2) |
| **GraphQL Endpoint** | <http://127.0.0.1:8000/graphql> | API GraphQL |
| **GraphiQL Playground** | <http://127.0.0.1:8000/graphiql> | Explorador GraphQL |
| **Laravel Reverb Tester** | <http://127.0.0.1:8000/reverb-pusher-tester.html> | **Herramienta para probar Laravel Reverb (Protocolo Pusher)** |
| **WebSocket Server** | <ws://127.0.0.1:8080> | Servidor Laravel Reverb (solo protocolo Pusher) |

## Datos del Sistema

| Entidad | Descripción |
|---------|-------------|
| **Empleados** | Empleados registrados |
| **Vehículos** | Inventario de vehículos |
| **Sucursales** | Oficinas/sucursales |
| **Usuarios** | Usuarios del sistema |
| **Asignaciones** | **Tabla principal - Vehículo-Sucursal** |
| **Roles** | Roles de usuario |

## Ejemplos de Consultas GraphQL

**NOTA**: El campo `estadisticas` fue removido del schema por falta de resolver.

### Asignaciones (Tabla Principal)

```graphql
query {
  vehiculoSucursales {
    id
    vehiculo {
      placa
      marca
      modelo
    }
    sucursal {
      nombre
      ciudad
    }
    fecha_asignacion
  }
}
```

### Empleados Completos

```graphql
query {
  empleados {
    id_empleado
    nombre
    correo
    telefono
    cargo
  }
}
```

### Vehículos con Estado

```graphql
query {
  vehiculos {
    id_vehiculo
    placa
    marca
    modelo
    anio
    estado
    tipo_id
  }
}
```

### Crear Empleado (Mutation)

```graphql
mutation {
  crearEmpleado(input: {
    nombre: "Juan Pérez"
    correo: "juan@empresa.com"
    cargo: "Analista"
  }) {
    id_empleado
    nombre
    correo
  }
}
```

## Eventos WebSocket en Tiempo Real

- `vehiculo_sucursal.asignado` - Nueva asignación vehículo-sucursal
- `vehiculo_sucursal.actualizado` - Asignación modificada
- `empleado.creado` - Nuevo empleado registrado
- `vehiculo.estado_cambiado` - Cambio de estado de vehículo

## Testing WebSocket

### **⚠️ Importante: Laravel Reverb usa Protocolo Pusher**
**Laravel Reverb NO es un servidor WebSocket estándar.** Usa el protocolo Pusher que requiere autenticación y canales específicos.

### **❌ Por qué Postman NO funciona con Laravel Reverb:**
- **Postman WebSocket**: Espera protocolo RFC 6455 estándar
- **Laravel Reverb**: Implementa protocolo Pusher con App Key
- **URL esperada por Postman**: `ws://127.0.0.1:8080/`
- **URL requerida por Reverb**: `ws://127.0.0.1:8080/app/APP_KEY`

### **✅ SOLUCIÓN: Flujo Híbrido Postman + Browser**

#### **Estrategia Recomendada:**
1. **Postman** → Ejecutar mutaciones GraphQL
2. **Browser** → Observar eventos WebSocket en tiempo real

#### **Configuración Paso a Paso:**

**1. Preparar Servidores:**
```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Laravel Reverb WebSocket
php artisan reverb:start
```

**2. Herramientas de Testing:**

**A) Para GraphQL (Postman):**
- Importar: `postman_collection.json`
- URL: `http://127.0.0.1:8000/graphql`
- Script: Copiar `postman_testing_script.js` en pestaña "Tests"

**B) Para WebSocket (Browser):**
```bash
# Opción 1: Herramienta Completa con UI
http://127.0.0.1:8000/websocket-test.html

# Opción 2: Herramienta Simple Pusher
http://127.0.0.1:8000/reverb-pusher-tester.html
```

#### **Flujo de Testing Completo:**

**PREPARACIÓN:**
1. **Browser**: Conectar WebSocket
   - Ir a `websocket-test.html`
   - Clic "Conectar"
   - Clic "📡 Suscribirse a Todos"

2. **Postman**: Preparar colección
   - Importar `postman_collection.json`
   - Agregar script de testing en pestaña "Tests"

**TESTING EN PARALELO:**
```
┌─────────────────┐    ┌─────────────────┐
│    POSTMAN      │    │    BROWSER      │
│   (GraphQL)     │───▶│   (WebSocket)   │
│                 │    │                 │
│ 1. Crear User   │    │ ✅ UserCreado   │
│ 2. Update User  │    │ ✅ UserUpdate   │
│ 3. Delete User  │    │ ✅ UserDelete   │
└─────────────────┘    └─────────────────┘
```

### **📋 Archivos de Testing Disponibles:**

- `postman_collection_v2.json` - ✅ **Colección actualizada con schema exacto**
- `postman_testing_script.js` - ✅ **Script automático mejorado para Postman**
- `websocket-test.html` - ✅ **Herramienta completa con UI**
- `dashboard-v2.blade.php` - ✅ **Dashboard con filtros de eventos mejorados** 
- `POSTMAN_WEBSOCKET_GUIDE.md` - ✅ **Guía detallada**
- `start-dashboard-improved.bat` - ✅ **Inicia sistema completo mejorado**

### **🎯 Mejoras Implementadas:**

**Dashboard mejorado:**
- ❌ **Filtrado de eventos internos** de Pusher (`pusher_internal:*`)
- ✅ **Mensajes específicos** por entidad: "👥 Empleados creado", "🏢 Sucursales actualizado" 
- ✅ **Solo eventos reales** de negocio, sin spam de conexiones
- ✅ **Iconos específicos** para cada tipo de entidad y acción

**Postman actualizado:**
- ✅ **Schema exacto** del GraphQL implementado
- ✅ **Variables dinámicas** que se actualizan automáticamente
- ✅ **Datos aleatorios** para testing realista
- ✅ **Script de testing** que detecta automáticamente el tipo de operación

### **🚀 Inicio Rápido Mejorado:**

```bash
# Opción 1: Script completo mejorado
start-dashboard-improved.bat

# Opción 2: Manual 
php artisan serve              # Terminal 1
php artisan reverb:start       # Terminal 2
# Abrir: http://127.0.0.1:8000/dashboard-v2
```

### **🔧 Troubleshooting WebSocket:**

**Error: "Connection failed"**
```bash
# Verificar Reverb
php artisan reverb:start --host=127.0.0.1 --port=8080

# Verificar configuración
php artisan config:cache
```

**Error: "No events received"**
```bash
# Verificar Queue Worker
php artisan queue:work

# Verificar Broadcasting
php artisan queue:restart
```

## Tests del Sistema

### Ejecutar Tests GraphQL

```bash
php artisan test --filter GraphQL
```

### Verificar Schema GraphQL

```bash
php artisan lighthouse:print-schema
```

### Limpiar Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Tecnologías Utilizadas

- **Framework**: Laravel 12
- **Base de Datos**: PostgreSQL (Neon Cloud)
- **GraphQL**: Lighthouse PHP (nuwave/lighthouse ^6.62)
- **WebSockets**: Laravel Reverb (laravel/reverb ^1.0)
- **Frontend**: Blade + Bootstrap 5 + Chart.js
- **Cliente WebSocket**: Pusher JS + Laravel Echo
- **Testing**: PHPUnit con 50+ tests GraphQL

## Estado del Proyecto

### Sistema Completamente Funcional

- Dashboard con actualizaciones en tiempo real
- API GraphQL unificada funcionando perfectamente
- WebSockets configurados con eventos en tiempo real
- 50/50 tests GraphQL pasando exitosamente
- Schema GraphQL limpio sin errores de resolvers
- Panel de debug WebSocket operativo
- Archivos de inicio automatizados disponibles

## Archivos de Configuración Disponibles

- `start-servers.bat` - Script principal para iniciar servidores
- `stop-servers.bat` - Script para detener todos los servidores
- `postman_collection.json` - Colección completa para testing APIs
- `README.md` - Documentación del proyecto
