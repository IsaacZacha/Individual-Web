# 📋 Comandos de Testing

## Ejecutar todos los tests
pytest tests/ -v --tb=short

## Ejecutar tests con cobertura
pytest tests/ --cov=app --cov-report=html --cov-report=term

## Ejecutar tests específicos
pytest tests/test_main.py::TestGraphQLQueries::test_crear_cliente_graphql -v

## Ejecutar tests de WebSocket
pytest tests/test_main.py::TestWebSocketConnections -v

## Ejecutar tests de integración
pytest tests/test_main.py::TestIntegration -v

# 🐛 Comandos de Debug

## Ejecutar en modo debug
python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000 --log-level debug

## Probar conexión a base de datos
python -c "from app.database import database; import asyncio; asyncio.run(database.connect()); print('✅ Conexión exitosa')"

# 📊 Comandos útiles para desarrollo

## Generar requirements.txt actualizado
pip freeze > requirements.txt

## Limpiar cache de Python
find . -type d -name __pycache__ -exec rm -r {} +
find . -name "*.pyc" -delete

## Ver estructura del proyecto
tree /f

# 🔍 URLs importantes

## GraphQL Playground
http://localhost:8000/graphql

## Documentación automática (Swagger)
http://localhost:8000/docs

## Documentación alternativa (ReDoc)
http://localhost:8000/redoc

## Health Check
http://localhost:8000/health

## WebSocket Stats
http://localhost:8000/ws/stats

## Notificación de prueba
http://localhost:8000/test/notification/reserva
