import pytest
from fastapi.testclient import TestClient
from app.main import app

@pytest.fixture
def client():
    return TestClient(app)

def test_crud_empleado(client):
    headers = {"Authorization": "Bearer fake-token"}
    # CREATE
    data = {
        "nombre": "Empleado Test",
        "cargo": "Tester",
        "correo": "empleado@test.com",
        "telefono": "123456789"
    }
    resp = client.post("/empleados/", json=data, headers=headers)
    assert resp.status_code == 200
    empleado = resp.json()
    assert empleado["nombre"] == "Empleado Test"
    empleado_id = empleado["id_empleado"]

    # READ (get by id)
    resp = client.get(f"/empleados/{empleado_id}", headers=headers)
    assert resp.status_code == 200
    empleado = resp.json()
    assert empleado["correo"] == "empleado@test.com"

    # UPDATE
    update_data = {
        "nombre": "Empleado Actualizado",
        "cargo": "QA",
        "correo": "empleado@test.com",
        "telefono": "987654321"
    }
    resp = client.put(f"/empleados/{empleado_id}", json=update_data, headers=headers)
    assert resp.status_code == 200
    empleado = resp.json()
    assert empleado["nombre"] == "Empleado Actualizado"
    assert empleado["telefono"] == "987654321"

    # LIST (get all)
    resp = client.get("/empleados/", headers=headers)
    assert resp.status_code == 200
    empleados = resp.json()
    assert any(e["id_empleado"] == empleado_id for e in empleados)

    # DELETE
    resp = client.delete(f"/empleados/{empleado_id}", headers=headers)
    assert resp.status_code == 200

    # VERIFY DELETE
    resp = client.get(f"/empleados/{empleado_id}", headers=headers)
    assert resp.status_code == 404