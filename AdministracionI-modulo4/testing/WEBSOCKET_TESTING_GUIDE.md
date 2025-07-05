# 🔌 **WebSocket Events Testing Guide**

## 📋 **Eventos Disponibles por Entidad**

### 👥 **EMPLEADOS**
- **empleado.creado** - Canal: `empleados`, `dashboard`
- **empleado.actualizado** - Canal: `empleados`, `dashboard`
- **empleado.eliminado** - Canal: `empleados`, `dashboard`

### 🚗 **VEHÍCULOS**
- **vehiculo.creado** - Canal: `vehiculos`, `dashboard`
- **vehiculo.actualizado** - Canal: `vehiculos`, `dashboard`
- **vehiculo.eliminado** - Canal: `vehiculos`, `dashboard`
- **vehiculo.estado.cambiado** - Canal: `vehiculos`, `dashboard`

### 🏢 **SUCURSALES**
- **sucursal.creada** - Canal: `sucursales`, `dashboard`
- **sucursal.actualizada** - Canal: `sucursales`, `dashboard`
- **sucursal.eliminada** - Canal: `sucursales`, `dashboard`

### 👤 **USUARIOS**
- **usuario.creado** - Canal: `usuarios`, `dashboard`
- **usuario.actualizado** - Canal: `usuarios`, `dashboard`
- **usuario.eliminado** - Canal: `usuarios`, `dashboard`

### 🔗 **ASIGNACIONES**
- **vehiculo.asignado** - Canal: `asignaciones`, `dashboard`
- **vehiculo.desasignado** - Canal: `asignaciones`, `dashboard`
- **asignacion.actualizada** - Canal: `asignaciones`, `dashboard`

---

## 🧪 **Endpoints de Testing WebSocket**

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/websocket-test/empleado-creado` | GET | Dispara evento `empleado.creado` |
| `/websocket-test/vehiculo-creado` | GET | Dispara evento `vehiculo.creado` |
| `/websocket-test/general-event` | GET | Dispara evento general de prueba |
| `/websocket-test/multiple-events` | GET | Dispara múltiples eventos secuenciales |
| `/websocket-test/status` | GET | Estado del sistema WebSocket |

---

## 📱 **Testing con Postman WebSocket**

### 1️⃣ **Configurar Conexión WebSocket**
```
URL: ws://127.0.0.1:8080/app/local
Protocol: pusher
```

### 2️⃣ **Suscribirse a Canales**
```javascript
// Mensaje de suscripción
{
    "event": "pusher:subscribe",
    "data": {
        "channel": "empleados"
    }
}

// Para múltiples canales
{
    "event": "pusher:subscribe",
    "data": {
        "channel": "dashboard"
    }
}
```

### 3️⃣ **Escuchar Eventos**
Los eventos llegarán con esta estructura:
```javascript
{
    "event": "empleado.creado",
    "channel": "empleados",
    "data": {
        "empleado": {
            "id_empleado": 999,
            "nombre": "Juan Test",
            "cargo": "WebSocket",
            "correo": "test@websocket.com"
        },
        "message": "Nuevo empleado creado: Juan Test",
        "timestamp": "2025-07-27T06:15:00.000000Z",
        "entity_type": "empleado",
        "action": "created",
        "type": "empleado.creado"
    }
}
```

---

## 🌐 **Testing con Navegador**

### **JavaScript para Suscribirse**
```javascript
// Conectar a Laravel Echo
const echo = new Echo({
    broadcaster: 'reverb',
    key: 'local',
    wsHost: '127.0.0.1',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss']
});

// Suscribirse a canal empleados
echo.channel('empleados')
    .listen('.empleado.creado', (data) => {
        console.log('🆕 Empleado creado:', data);
    })
    .listen('.empleado.actualizado', (data) => {
        console.log('✏️ Empleado actualizado:', data);
    })
    .listen('.empleado.eliminado', (data) => {
        console.log('🗑️ Empleado eliminado:', data);
    });

// Suscribirse a canal dashboard (todos los eventos)
echo.channel('dashboard')
    .listen('.empleado.creado', (data) => {
        console.log('📊 Dashboard - Empleado creado:', data);
    })
    .listen('.vehiculo.creado', (data) => {
        console.log('📊 Dashboard - Vehículo creado:', data);
    });
```

---

## 🔧 **Comandos de Testing**

### **Disparar Eventos desde Terminal**
```bash
# Test empleado
curl http://localhost:8000/websocket-test/empleado-creado

# Test vehículo
curl http://localhost:8000/websocket-test/vehiculo-creado

# Estado del sistema
curl http://localhost:8000/websocket-test/status
```

### **Verificar Estado de Laravel Reverb**
```bash
# Verificar proceso
netstat -an | findstr :8080

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 📊 **Datos de Prueba**

### **Empleado Test**
```json
{
    "id_empleado": 999,
    "nombre": "Juan Test",
    "apellido": "WebSocket",
    "email": "test@websocket.com",
    "telefono": "123456789",
    "direccion": "Calle Test 123",
    "fecha_contratacion": "2025-07-27",
    "salario": 50000,
    "estado": "activo",
    "rol_id": 1
}
```

### **Vehículo Test**
```json
{
    "id_vehiculo": 999,
    "marca": "Toyota",
    "modelo": "Test WebSocket",
    "anio": 2024,
    "placa": "WS-999",
    "color": "Azul",
    "numero_motor": "TEST123456",
    "numero_chasis": "CHASIS999",
    "estado": "disponible",
    "kilometraje": 0
}
```

---

## ✅ **Checklist de Testing**

- [ ] **Conexión WebSocket**: ¿Se conecta a `ws://127.0.0.1:8080`?
- [ ] **Suscripción a Canales**: ¿Se suscribe correctamente a `empleados`, `vehiculos`, `dashboard`?
- [ ] **Eventos Empleados**: ¿Llegan eventos `empleado.creado`, `empleado.actualizado`, `empleado.eliminado`?
- [ ] **Eventos Vehículos**: ¿Llegan eventos `vehiculo.creado`, `vehiculo.actualizado`, `vehiculo.eliminado`?
- [ ] **Dashboard**: ¿Se actualiza la actividad reciente en tiempo real?
- [ ] **GraphQL + WebSocket**: ¿Las mutations de GraphQL disparan eventos WebSocket?
- [ ] **Broadcasting**: ¿Los eventos se envían a múltiples canales correctamente?

---

## 🚨 **Troubleshooting**

### **Error: Cannot connect to WebSocket**
```bash
# Verificar Laravel Reverb
php artisan reverb:start --debug

# Verificar puerto
netstat -an | findstr :8080
```

### **Error: Events not received**
```bash
# Verificar queue worker
php artisan queue:work --timeout=0

# Verificar configuración broadcasting
php artisan config:cache
```

### **Error: Authentication failed**
- Verificar `REVERB_APP_KEY` en `.env`
- Revisar configuración en `config/broadcasting.php`
