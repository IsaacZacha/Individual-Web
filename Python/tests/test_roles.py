import pytest
from fastapi.testclient import TestClient
from app.main import app

@pytest.fixture
def client():
    return TestClient(app)

def test_crud_rol(client):
    headers = {"Authorization": "Bearer fake-token"}
    # CREATE
    data = {
        "nombre": "Rol Test",
    }
    resp = client.post("/roles/", json=data, headers=headers)
    assert resp.status_code == 200
    rol = resp.json()
    assert rol["nombre"] == "Rol Test"
    rol_id = rol["id_rol"]

    # READ
    resp = client.get(f"/roles/{rol_id}", headers=headers)
    assert resp.status_code == 200
    rol = resp.json()
    assert rol["nombre"] == "Rol Test"

    # UPDATE
    update_data = {
        "nombre": "Rol Actualizado",
    }
    resp = client.put(f"/roles/{rol_id}", json=update_data, headers=headers)
    assert resp.status_code == 200
    rol = resp.json()
    assert rol["nombre"] == "Rol Actualizado"

    # LIST
    resp = client.get("/roles/", headers=headers)
    assert resp.status_code == 200
    roles = resp.json()
    assert any(r["id_rol"] == rol_id for r in roles)

    # DELETE
    resp = client.delete(f"/roles/{rol_id}", headers=headers)
    assert resp.status_code == 200

    # VERIFY DELETE
    resp = client.get(f"/roles/{rol_id}", headers=headers)
    assert resp.status_code == 404