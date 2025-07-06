#!/bin/bash

echo "🚀 Iniciando Sistema de Alquiler de Vehículos - Python"
echo "=================================================="

# Verificar si Python está instalado
if ! command -v python &> /dev/null; then
    echo "❌ Python no está instalado. Por favor, instala Python 3.8 o superior."
    exit 1
fi

# Crear entorno virtual si no existe
if [ ! -d "venv" ]; then
    echo "📦 Creando entorno virtual..."
    python -m venv venv
fi

# Activar entorno virtual
echo "🔧 Activando entorno virtual..."
if [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "win32" ]]; then
    source venv/Scripts/activate
else
    source venv/bin/activate
fi

# Instalar dependencias
echo "📚 Instalando dependencias..."
pip install -r requirements.txt

# Ejecutar aplicación
echo "🌟 Iniciando servidor..."
echo "GraphQL Playground: http://localhost:8000/graphql"
echo "API Documentation: http://localhost:8000/docs"
echo "WebSocket Test: ws://localhost:8000/ws/general"
echo ""

python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
