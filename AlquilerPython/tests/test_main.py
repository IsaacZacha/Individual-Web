import pytest
import asyncio
from fastapi.testclient import TestClient
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from app.main import app
from app.database import get_db, Base
from app.models import Cliente, Vehiculo, Reserva

# Base de datos de prueba en memoria
SQLALCHEMY_DATABASE_URL = "sqlite:///./test.db"
engine = create_engine(SQLALCHEMY_DATABASE_URL, connect_args={"check_same_thread": False})
TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

def override_get_db():
    try:
        db = TestingSessionLocal()
        yield db
    finally:
        db.close()

app.dependency_overrides[get_db] = override_get_db

@pytest.fixture
def setup_database():
    """Configurar base de datos de prueba"""
    Base.metadata.create_all(bind=engine)
    yield
    Base.metadata.drop_all(bind=engine)

@pytest.fixture
def client():
    """Cliente de prueba para FastAPI"""
    return TestClient(app)

@pytest.fixture
def sample_data():
    """Datos de ejemplo para las pruebas"""
    return {
        "cliente": {
            "nombre": "Juan Pérez Test",
            "email": "juan.test@email.com"
        },
        "vehiculo": {
            "modelo": "Toyota Corolla Test",
            "placa": "TEST-123"
        },
        "reserva": {
            "clienteId": 1,
            "vehiculoId": 1,
            "fechaReserva": "2025-07-20",
            "fechaInicio": "2025-07-25",
            "fechaFin": "2025-07-30",
            "estado": "ACTIVA"
        }
    }

class TestGraphQLQueries:
    """Pruebas para las consultas GraphQL"""
    
    def test_health_endpoint(self, client):
        """Probar endpoint de salud"""
        response = client.get("/health")
        assert response.status_code == 200
        data = response.json()
        assert data["status"] == "healthy"
    
    def test_root_endpoint(self, client):
        """Probar endpoint raíz"""
        response = client.get("/")
        assert response.status_code == 200
        data = response.json()
        assert "message" in data
        assert "graphql_endpoint" in data
    
    def test_crear_cliente_graphql(self, client, setup_database, sample_data):
        """Probar creación de cliente via GraphQL"""
        query = """
        mutation CrearCliente($clienteData: ClienteInput!) {
            crearCliente(clienteData: $clienteData) {
                id
                nombre
                email
            }
        }
        """
        
        variables = {
            "clienteData": sample_data["cliente"]
        }
        
        response = client.post(
            "/graphql",
            json={"query": query, "variables": variables}
        )
        
        assert response.status_code == 200
        data = response.json()
        assert "data" in data
        assert data["data"]["crearCliente"]["nombre"] == sample_data["cliente"]["nombre"]
        assert data["data"]["crearCliente"]["email"] == sample_data["cliente"]["email"]
    
    def test_obtener_clientes_graphql(self, client, setup_database):
        """Probar consulta de clientes via GraphQL"""
        query = """
        query {
            clientes {
                id
                nombre
                email
            }
        }
        """
        
        response = client.post(
            "/graphql",
            json={"query": query}
        )
        
        assert response.status_code == 200
        data = response.json()
        assert "data" in data
        assert "clientes" in data["data"]
        assert isinstance(data["data"]["clientes"], list)
    
    def test_crear_vehiculo_graphql(self, client, setup_database, sample_data):
        """Probar creación de vehículo via GraphQL"""
        query = """
        mutation CrearVehiculo($vehiculoData: VehiculoInput!) {
            crearVehiculo(vehiculoData: $vehiculoData) {
                id
                modelo
                placa
            }
        }
        """
        
        variables = {
            "vehiculoData": sample_data["vehiculo"]
        }
        
        response = client.post(
            "/graphql",
            json={"query": query, "variables": variables}
        )
        
        assert response.status_code == 200
        data = response.json()
        assert "data" in data
        assert data["data"]["crearVehiculo"]["modelo"] == sample_data["vehiculo"]["modelo"]
        assert data["data"]["crearVehiculo"]["placa"] == sample_data["vehiculo"]["placa"]

class TestWebSocketConnections:
    """Pruebas para conexiones WebSocket"""
    
    def test_websocket_connection(self, client):
        """Probar conexión WebSocket básica"""
        with client.websocket_connect("/ws/general") as websocket:
            # Debería recibir mensaje de bienvenida
            data = websocket.receive_json()
            assert data["type"] == "connection"
            assert "Conectado al canal" in data["message"]
    
    def test_websocket_echo(self, client):
        """Probar funcionalidad echo del WebSocket"""
        with client.websocket_connect("/ws/test") as websocket:
            # Recibir mensaje de bienvenida
            welcome = websocket.receive_json()
            assert welcome["type"] == "connection"
            
            # Enviar mensaje de prueba
            test_message = "Hola WebSocket"
            websocket.send_text(test_message)
            
            # Recibir respuesta echo
            response = websocket.receive_json()
            assert response["type"] == "echo"
            assert response["received"] == test_message
    
    def test_websocket_stats(self, client):
        """Probar endpoint de estadísticas WebSocket"""
        response = client.get("/ws/stats")
        assert response.status_code == 200
        data = response.json()
        assert "active_connections" in data
        assert "total_connections" in data

class TestNotifications:
    """Pruebas para el sistema de notificaciones"""
    
    def test_notification_reserva(self, client):
        """Probar notificación de nueva reserva"""
        response = client.get("/test/notification/reserva")
        assert response.status_code == 200
        data = response.json()
        assert "message" in data
        assert "Notificación reserva enviada" in data["message"]
    
    def test_notification_alquiler(self, client):
        """Probar notificación de estado de alquiler"""
        response = client.get("/test/notification/alquiler")
        assert response.status_code == 200
        data = response.json()
        assert "message" in data
        assert "Notificación alquiler enviada" in data["message"]
    
    def test_notification_pago(self, client):
        """Probar notificación de nuevo pago"""
        response = client.get("/test/notification/pago")
        assert response.status_code == 200
        data = response.json()
        assert "message" in data
        assert "Notificación pago enviada" in data["message"]

class TestIntegration:
    """Pruebas de integración completas"""
    
    def test_flujo_completo_reserva(self, client, setup_database, sample_data):
        """Probar flujo completo: Cliente -> Vehículo -> Reserva"""
        # 1. Crear cliente
        client_query = """
        mutation CrearCliente($clienteData: ClienteInput!) {
            crearCliente(clienteData: $clienteData) {
                id
                nombre
                email
            }
        }
        """
        
        client_response = client.post(
            "/graphql",
            json={
                "query": client_query,
                "variables": {"clienteData": sample_data["cliente"]}
            }
        )
        
        assert client_response.status_code == 200
        cliente_data = client_response.json()["data"]["crearCliente"]
        cliente_id = cliente_data["id"]
        
        # 2. Crear vehículo
        vehiculo_query = """
        mutation CrearVehiculo($vehiculoData: VehiculoInput!) {
            crearVehiculo(vehiculoData: $vehiculoData) {
                id
                modelo
                placa
            }
        }
        """
        
        vehiculo_response = client.post(
            "/graphql",
            json={
                "query": vehiculo_query,
                "variables": {"vehiculoData": sample_data["vehiculo"]}
            }
        )
        
        assert vehiculo_response.status_code == 200
        vehiculo_data = vehiculo_response.json()["data"]["crearVehiculo"]
        vehiculo_id = vehiculo_data["id"]
        
        # 3. Crear reserva
        reserva_query = """
        mutation CrearReserva($reservaData: ReservaInput!) {
            crearReserva(reservaData: $reservaData) {
                idReserva
                clienteId
                vehiculoId
                estado
            }
        }
        """
        
        reserva_data = {
            "clienteId": cliente_id,
            "vehiculoId": vehiculo_id,
            "fechaReserva": "2025-07-20",
            "fechaInicio": "2025-07-25",
            "fechaFin": "2025-07-30",
            "estado": "ACTIVA"
        }
        
        reserva_response = client.post(
            "/graphql",
            json={
                "query": reserva_query,
                "variables": {"reservaData": reserva_data}
            }
        )
        
        assert reserva_response.status_code == 200
        reserva_result = reserva_response.json()["data"]["crearReserva"]
        assert reserva_result["clienteId"] == cliente_id
        assert reserva_result["vehiculoId"] == vehiculo_id
        assert reserva_result["estado"] == "ACTIVA"

# Configuración de pytest
if __name__ == "__main__":
    pytest.main([__file__, "-v"])
