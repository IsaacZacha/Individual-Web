@echo off
echo ====================================
echo  INICIANDO SERVIDORES LARAVEL
echo ====================================
echo.

echo [1/2] Iniciando Laravel Reverb (WebSocket Server)...
echo Puerto: 8080
echo URL: ws://127.0.0.1:8080
start "Laravel Reverb" cmd /k "php artisan reverb:start"

echo.
echo [2/2] Iniciando Laravel Application Server...
echo Puerto: 8000  
echo URL: http://127.0.0.1:8000
echo GraphQL: http://127.0.0.1:8000/graphql
echo API REST: http://127.0.0.1:8000/api
start "Laravel Server" cmd /k "php artisan serve"

echo.
echo ====================================
echo  SERVIDORES INICIADOS
echo ====================================
echo WebSocket: ws://127.0.0.1:8080
echo Application: http://127.0.0.1:8000
echo.
echo Presiona cualquier tecla para cerrar esta ventana...
pause >nul
