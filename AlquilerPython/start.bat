@echo off
echo 🚀 Iniciando Sistema de Alquiler de Vehículos - Python
echo ==================================================

REM Verificar si Python está instalado
python --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Python no está instalado. Por favor, instala Python 3.8 o superior.
    pause
    exit /b 1
)

REM Crear entorno virtual si no existe
if not exist venv (
    echo 📦 Creando entorno virtual...
    python -m venv venv
)

REM Activar entorno virtual
echo 🔧 Activando entorno virtual...
call venv\Scripts\activate.bat

REM Instalar dependencias
echo 📚 Instalando dependencias...
pip install -r requirements.txt

REM Ejecutar aplicación
echo 🌟 Iniciando servidor...
echo GraphQL Playground: http://localhost:8000/graphql
echo API Documentation: http://localhost:8000/docs
echo WebSocket Test: ws://localhost:8000/ws/general
echo.

python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000

pause
