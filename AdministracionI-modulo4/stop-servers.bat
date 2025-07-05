@echo off
echo ====================================
echo  DETENIENDO SERVIDORES LARAVEL  
echo ====================================
echo.

echo Cerrando procesos de Laravel y Reverb...

:: Buscar y terminar procesos de artisan serve
for /f "tokens=2" %%i in ('tasklist /fi "imagename eq php.exe" /fi "windowtitle eq Laravel Server*" /fo csv ^| find "php.exe"') do (
    echo Cerrando Laravel Server (PID: %%i)
    taskkill /pid %%i /f >nul 2>&1
)

:: Buscar y terminar procesos de artisan reverb
for /f "tokens=2" %%i in ('tasklist /fi "imagename eq php.exe" /fi "windowtitle eq Laravel Reverb*" /fo csv ^| find "php.exe"') do (
    echo Cerrando Laravel Reverb (PID: %%i)  
    taskkill /pid %%i /f >nul 2>&1
)

:: Método alternativo - cerrar por puerto
echo Cerrando procesos en puertos 8000 y 8080...
netstat -ano | findstr :8000 | findstr LISTENING >nul 2>&1
if %errorlevel% equ 0 (
    for /f "tokens=5" %%p in ('netstat -ano ^| findstr :8000 ^| findstr LISTENING') do taskkill /pid %%p /f >nul 2>&1
)

netstat -ano | findstr :8080 | findstr LISTENING >nul 2>&1  
if %errorlevel% equ 0 (
    for /f "tokens=5" %%p in ('netstat -ano ^| findstr :8080 ^| findstr LISTENING') do taskkill /pid %%p /f >nul 2>&1
)

echo.
echo ====================================
echo  SERVIDORES DETENIDOS
echo ====================================
echo.
echo Presiona cualquier tecla para continuar...
pause >nul
