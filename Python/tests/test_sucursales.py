import pytest
from fastapi.testclient import TestClient
from app.main import app

@pytest.fixture
def client():
    return TestClient(app)

def test_crud_sucursal(client):
    headers = {"Authorization": "Bearer fake-token"}
    # CREATE
    data = {
        "nombre": "Sucursal Test",
        "direccion": "Calle Falsa 123",
        "ciudad": "Ciudad Test",
        "telefono": "555-1234"
    }
    resp = client.post("/sucursales/", json=data, headers=headers)
    assert resp.status_code == 200
    sucursal = resp.json()
    assert sucursal["nombre"] == "Sucursal Test"
    sucursal_id = sucursal["id_sucursal"]

    # READ
    resp = client.get(f"/sucursales/{sucursal_id}", headers=headers)
    assert resp.status_code == 200
    sucursal = resp.json()
    assert sucursal["direccion"] == "Calle Falsa 123"

    # UPDATE
    update_data = {
        "nombre": "Sucursal Actualizada",
        "direccion": "Avenida Siempreviva 742",
        "ciudad": "Ciudad Actualizada",
        "telefono": "555-4321"
    }
    resp = client.put(f"/sucursales/{sucursal_id}", json=update_data, headers=headers)
    assert resp.status_code == 200
    sucursal = resp.json()
    assert sucursal["nombre"] == "Sucursal Actualizada"

    # LIST
    resp = client.get("/sucursales/", headers=headers)
    assert resp.status_code == 200
    sucursales = resp.json()
    assert any(s["id_sucursal"] == sucursal_id for s in sucursales)

    # DELETE
    resp = client.delete(f"/sucursales/{sucursal_id}", headers=headers)
    assert resp.status_code == 200

    # VERIFY DELETE
    resp = client.get(f"/sucursales/{sucursal_id}", headers=headers)
    assert resp.status_code == 404