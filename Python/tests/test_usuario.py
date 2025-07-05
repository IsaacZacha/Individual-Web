import pytest
from fastapi.testclient import TestClient
from app.main import app

@pytest.fixture
def client():
    return TestClient(app)

def test_crud_usuario(client):
    headers = {"Authorization": "Bearer fake-token"}

    # Crear empleado necesario para la FK
    empleado_data = {
        "nombre": "Empleado Test",
        "cargo": "Tester",
        "correo": "empleado_usuario2@test.com",
        "telefono": "123456789"
    }
    resp = client.post("/empleados/", json=empleado_data, headers=headers)
    assert resp.status_code == 200
    empleado_id = resp.json()["id_empleado"]

    # Crear rol necesario para la FK
    rol_data = {
        "nombre": "Rol Test",
        "descripcion": "Rol para usuario"
    }
    resp = client.post("/roles/", json=rol_data, headers=headers)
    assert resp.status_code == 200
    rol_id = resp.json()["id_rol"]

    # CREATE usuario
    data = {
        "empleado_id": empleado_id,
        "username": "usuario_test",
        "rol_id": rol_id,
        "contrasena": "123456"
    }
    resp = client.post("/usuarios/", json=data, headers=headers)
    assert resp.status_code == 200, resp.text
    usuario = resp.json()
    assert usuario["username"] == "usuario_test"
    usuario_id = usuario["id_usuario"]

    # READ
    resp = client.get(f"/usuarios/{usuario_id}", headers=headers)
    assert resp.status_code == 200
    usuario = resp.json()
    assert usuario["username"] == "usuario_test"

    # UPDATE
    update_data = {
        "empleado_id": empleado_id,
        "username": "usuario_actualizado",
        "rol_id": rol_id,
        "contrasena": "654321"
    }
    resp = client.put(f"/usuarios/{usuario_id}", json=update_data, headers=headers)
    assert resp.status_code == 200
    usuario = resp.json()
    assert usuario["username"] == "usuario_actualizado"

    # LIST
    resp = client.get("/usuarios/", headers=headers)
    assert resp.status_code == 200
    usuarios = resp.json()
    assert any(u["id_usuario"] == usuario_id for u in usuarios)

    # DELETE usuario
    resp = client.delete(f"/usuarios/{usuario_id}", headers=headers)
    assert resp.status_code == 200

    # VERIFY DELETE usuario
    resp = client.get(f"/usuarios/{usuario_id}", headers=headers)
    assert resp.status_code == 404

    # DELETE empleado creado en el test
    resp = client.delete(f"/empleados/{empleado_id}", headers=headers)
    assert resp.status_code == 200