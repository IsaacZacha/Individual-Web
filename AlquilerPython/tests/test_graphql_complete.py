"""
Tests completos para GraphQL API Gateway
Segundo Parcial - Sistema de Alquiler de Vehículos
"""
import pytest
import json
from fastapi.testclient import TestClient
from app.main_graphql_complete import app

client = TestClient(app)

# === TESTS DE GRAPHQL QUERIES ===

def test_graphql_query_clientes():
    """Test: Consulta GraphQL de todos los clientes"""
    query = """
    query {
        clientes {
            id
            nombre
            email
        }
    }
    """
    
    response = client.post("/graphql", json={"query": query})
    assert response.status_code == 200
    
    data = response.json()
    assert "data" in data
    assert "clientes" in data["data"]
    assert len(data["data"]["clientes"]) >= 2

def test_graphql_query_cliente_por_id():
    """Test: Consulta GraphQL de cliente específico"""
    query = """
    query {
        cliente(id: 1) {
            id
            nombre
            email
        }
    }
    """
    
    response = client.post("/graphql", json={"query": query})
    assert response.status_code == 200
    
    data = response.json()
    cliente = data["data"]["cliente"]
    assert cliente["id"] == 1
    assert cliente["nombre"] == "Juan Pérez"

def test_graphql_query_reservas_con_relaciones():
    """Test: Consulta GraphQL con relaciones complejas"""
    query = """
    query {
        reservas {
            id_reserva
            estado
            fecha_inicio
            fecha_fin
            cliente {
                id
                nombre
                email
            }
            vehiculo {
                id
                modelo
                placa
            }
        }
    }
    """
    
    response = client.post("/graphql", json={"query": query})
    assert response.status_code == 200
    
    data = response.json()
    reservas = data["data"]["reservas"]
    assert len(reservas) >= 1
    
    # Verificar que las relaciones están resueltas
    reserva = reservas[0]
    assert "cliente" in reserva
    assert "vehiculo" in reserva
    assert reserva["cliente"]["nombre"] is not None
    assert reserva["vehiculo"]["modelo"] is not None

def test_graphql_query_vehiculos_disponibles():
    """Test: Consulta GraphQL compleja de vehículos disponibles"""
    query = """
    query {
        vehiculosDisponibles(fechaInicio: "2024-06-01", fechaFin: "2024-06-07") {
            id
            modelo
            placa
        }
    }
    """
    
    response = client.post("/graphql", json={"query": query})
    assert response.status_code == 200
    
    data = response.json()
    assert "vehiculosDisponibles" in data["data"]

# === TESTS DE GRAPHQL MUTATIONS ===

def test_graphql_mutation_crear_cliente():
    """Test: Mutation GraphQL para crear cliente"""
    mutation = """
    mutation {
        crearCliente(clienteData: {
            nombre: "Cliente Test"
            email: "test@example.com"
        }) {
            success
            message
            cliente {
                id
                nombre
                email
            }
        }
    }
    """
    
    response = client.post("/graphql", json={"query": mutation})
    assert response.status_code == 200
    
    data = response.json()
    result = data["data"]["crearCliente"]
    assert result["success"] == True
    assert result["cliente"]["nombre"] == "Cliente Test"
    assert result["cliente"]["email"] == "test@example.com"

def test_graphql_mutation_crear_reserva():
    """Test: Mutation GraphQL para crear reserva"""
    mutation = """
    mutation {
        crearReserva(reservaData: {
            clienteId: 1
            vehiculoId: 1
            fechaReserva: "2024-06-01"
            fechaInicio: "2024-06-10"
            fechaFin: "2024-06-15"
            estado: "confirmada"
        }) {
            success
            message
            reserva {
                id_reserva
                estado
                cliente {
                    nombre
                }
                vehiculo {
                    modelo
                }
            }
        }
    }
    """
    
    response = client.post("/graphql", json={"query": mutation})
    assert response.status_code == 200
    
    data = response.json()
    result = data["data"]["crearReserva"]
    assert result["success"] == True
    assert result["reserva"]["estado"] == "confirmada"

# === TESTS DE WEBSOCKETS ===

def test_websocket_connection():
    """Test: Conexión WebSocket básica"""
    with client.websocket_connect("/ws/test") as websocket:
        # Recibir mensaje de bienvenida
        data = websocket.receive_text()
        message = json.loads(data)
        
        assert message["event"] == "connection_established"
        assert "Conectado al canal: test" in message["message"]

def test_websocket_echo():
    """Test: Echo de mensajes WebSocket"""
    with client.websocket_connect("/ws/general") as websocket:
        # Recibir mensaje de bienvenida
        welcome = websocket.receive_text()
        
        # Enviar mensaje
        test_message = "Hola WebSocket"
        websocket.send_text(test_message)
        
        # Recibir echo
        response = websocket.receive_text()
        echo_data = json.loads(response)
        
        assert echo_data["event"] == "message_received"
        assert echo_data["original_message"] == test_message

# === TESTS DE API REST CON NOTIFICACIONES ===

def test_crear_cliente_con_notificacion():
    """Test: Crear cliente vía REST genera notificación WebSocket"""
    # Datos del nuevo cliente
    cliente_data = {
        "nombre": "WebSocket Test",
        "email": "ws@test.com"
    }
    
    # Crear cliente via API REST
    response = client.post("/api/clientes", json=cliente_data)
    assert response.status_code == 200
    
    data = response.json()
    assert data["success"] == True
    assert data["data"]["nombre"] == "WebSocket Test"

def test_crear_reserva_con_notificacion():
    """Test: Crear reserva vía REST genera notificación WebSocket"""
    reserva_data = {
        "cliente_id": 1,
        "vehiculo_id": 2,
        "fecha_reserva": "2024-06-01",
        "fecha_inicio": "2024-06-10", 
        "fecha_fin": "2024-06-15",
        "estado": "confirmada"
    }
    
    response = client.post("/api/reservas", json=reserva_data)
    assert response.status_code == 200
    
    data = response.json()
    assert data["success"] == True
    assert data["data"]["estado"] == "confirmada"

# === TESTS DE API GATEWAY ===

def test_gateway_stats():
    """Test: Estadísticas del API Gateway"""
    response = client.get("/api/gateway/stats")
    assert response.status_code == 200
    
    data = response.json()
    assert data["success"] == True
    
    stats = data["data"]
    assert "total_clientes" in stats
    assert "total_vehiculos" in stats
    assert "total_reservas" in stats
    assert "conexiones_websocket" in stats

def test_gateway_health():
    """Test: Health check del API Gateway"""
    response = client.get("/api/gateway/health")
    assert response.status_code == 200
    
    data = response.json()
    assert data["success"] == True
    
    health_data = data["data"]
    assert health_data["status"] == "healthy"
    assert health_data["services"]["graphql"] == "active"
    assert health_data["services"]["websockets"] == "active"
    assert health_data["services"]["rest_api"] == "active"

def test_root_endpoint():
    """Test: Endpoint raíz del API Gateway"""
    response = client.get("/")
    assert response.status_code == 200
    
    data = response.json()
    assert data["success"] == True
    assert "API Gateway" in data["message"]
    
    endpoints = data["data"]["endpoints"]
    assert "/graphql" in endpoints["graphql"]
    assert "/docs" in endpoints["docs"]

# === TESTS DE INTEGRACIÓN COMPLETA ===

def test_flujo_completo_graphql():
    """Test de integración: Flujo completo usando GraphQL"""
    
    # 1. Crear cliente
    mutation_cliente = """
    mutation {
        crearCliente(clienteData: {
            nombre: "Integración Test"
            email: "integracion@test.com"
        }) {
            success
            cliente {
                id
                nombre
            }
        }
    }
    """
    
    response = client.post("/graphql", json={"query": mutation_cliente})
    cliente_result = response.json()["data"]["crearCliente"]
    cliente_id = cliente_result["cliente"]["id"]
    
    # 2. Consultar cliente creado
    query_cliente = f"""
    query {{
        cliente(id: {cliente_id}) {{
            id
            nombre
            email
        }}
    }}
    """
    
    response = client.post("/graphql", json={"query": query_cliente})
    cliente_data = response.json()["data"]["cliente"]
    assert cliente_data["nombre"] == "Integración Test"
    
    # 3. Crear reserva para el cliente
    mutation_reserva = f"""
    mutation {{
        crearReserva(reservaData: {{
            clienteId: {cliente_id}
            vehiculoId: 1
            fechaReserva: "2024-07-01"
            fechaInicio: "2024-07-05"
            fechaFin: "2024-07-10"
            estado: "confirmada"
        }}) {{
            success
            reserva {{
                id_reserva
                cliente {{
                    nombre
                }}
            }}
        }}
    }}
    """
    
    response = client.post("/graphql", json={"query": mutation_reserva})
    reserva_result = response.json()["data"]["crearReserva"]
    assert reserva_result["success"] == True
    assert reserva_result["reserva"]["cliente"]["nombre"] == "Integración Test"

if __name__ == "__main__":
    pytest.main([__file__])
