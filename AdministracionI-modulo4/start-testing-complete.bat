@echo off
echo 🚀 Iniciando Sistema Completo para Testing...
echo.

echo 📡 Paso 1: Iniciando Laravel Server...
start cmd /k "php artisan serve"
timeout /t 3

echo 🔌 Paso 2: Iniciando Laravel Reverb WebSocket...
start cmd /k "php artisan reverb:start"
timeout /t 3

echo 📊 Paso 3: Abriendo herramientas de testing...
start http://127.0.0.1:8000/websocket-test.html

echo.
echo ✅ SISTEMA INICIADO EXITOSAMENTE
echo.
echo 📋 PRÓXIMOS PASOS:
echo    1. En Browser: Conectar WebSocket y suscribirse a canales
echo    2. En Postman: Importar postman_collection.json
echo    3. En Postman: Ejecutar mutaciones GraphQL
echo    4. En Browser: Ver eventos WebSocket en tiempo real
echo.
echo 📄 Archivos disponibles:
echo    - postman_collection.json (Importar en Postman)
echo    - postman_testing_script.js (Script para Tests)
echo    - POSTMAN_WEBSOCKET_GUIDE.md (Guía completa)
echo.
echo 🔗 URLs importantes:
echo    - GraphQL: http://127.0.0.1:8000/graphql
echo    - WebSocket Test: http://127.0.0.1:8000/websocket-test.html
echo    - Laravel App: http://127.0.0.1:8000
echo.
echo ⚠️  IMPORTANTE: Laravel Reverb usa protocolo Pusher, NO WebSocket directo
echo    Por eso Postman WebSocket NO funciona, usa el flujo híbrido recomendado
echo.
pause
