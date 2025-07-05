@echo off
echo 🚀 Iniciando Sistema Completo con Dashboard Mejorado...
echo.

echo 📡 Paso 1: Iniciando Laravel Server...
start cmd /k "cd /d %~dp0 && php artisan serve"
timeout /t 3

echo 🔌 Paso 2: Iniciando Laravel Reverb WebSocket...
start cmd /k "cd /d %~dp0 && php artisan reverb:start"
timeout /t 5

echo 📊 Paso 3: Abriendo Dashboard Mejorado...
start http://127.0.0.1:8000/dashboard-v2

echo 🔧 Paso 4: Abriendo herramientas adicionales...
timeout /t 2
start http://127.0.0.1:8000/websocket-test.html

echo.
echo ✅ SISTEMA INICIADO EXITOSAMENTE
echo.
echo 📋 MEJORAS IMPLEMENTADAS:
echo    ✅ Dashboard filtrea eventos internos de Pusher
echo    ✅ Mensajes específicos por entidad (Empleados, Sucursales, etc)
echo    ✅ Colección Postman actualizada con schema exacto
echo    ✅ Variables dinámicas en Postman
echo    ✅ Script de testing mejorado
echo.
echo 🎯 FLUJO DE TESTING RECOMENDADO:
echo    1. Dashboard: http://127.0.0.1:8000/dashboard-v2
echo    2. Postman: Importar postman_collection_v2.json
echo    3. Postman: Agregar postman_testing_script.js en Tests
echo    4. Ejecutar mutations en Postman
echo    5. Ver eventos específicos en Dashboard
echo.
echo 📄 Archivos actualizados:
echo    - postman_collection_v2.json (Schema exacto)
echo    - postman_testing_script.js (Script mejorado) 
echo    - dashboard-v2.blade.php (Filtros y mensajes)
echo.
echo 🔗 URLs importantes:
echo    - GraphQL: http://127.0.0.1:8000/graphql
echo    - Dashboard: http://127.0.0.1:8000/dashboard-v2
echo    - WebSocket Test: http://127.0.0.1:8000/websocket-test.html
echo.
echo 🎉 NOVEDADES:
echo    ❌ Ya NO verás "Elemento modificado"
echo    ✅ Verás "👥 Empleados creado" 
echo    ✅ Verás "🏢 Sucursales actualizado"
echo    ✅ Verás "🚗 Vehículos eliminado"
echo    ✅ Solo eventos reales, sin spam de Pusher
echo.
pause
