import pytest
from fastapi.testclient import TestClient
from app.main import app
from datetime import date

@pytest.fixture
def client():
    return TestClient(app)

@pytest.mark.asyncio
async def test_crud_vehiculo_sucursal(client, vehiculo_de_prueba):
    headers = {"Authorization": "Bearer fake-token"}

    # Crear sucursal
    sucursal_data = {
        "nombre": "Sucursal Test",
        "direccion": "Calle Falsa 123",
        "ciudad": "Ciudad Test",
        "telefono": "555-1234"
    }
    resp = client.post("/sucursales/", json=sucursal_data, headers=headers)
    assert resp.status_code == 200
    sucursal_id = resp.json()["id_sucursal"]

    vehiculo_id = await vehiculo_de_prueba  # Espera el fixture async

    # CREATE relación vehículo-sucursal
    data = {
        "vehiculo_id": vehiculo_id,
        "sucursal_id": sucursal_id,
        "fecha_ingreso": str(date.today())
    }
    resp = client.post("/vehiculos-sucursal/", json=data, headers=headers)
    assert resp.status_code == 200
    relacion = resp.json()
    assert relacion["vehiculo_id"] == vehiculo_id
    id_relacion = relacion["id_relacion"]

    # READ
    resp = client.get(f"/vehiculos-sucursal/{id_relacion}", headers=headers)
    assert resp.status_code == 200
    relacion = resp.json()
    assert relacion["sucursal_id"] == sucursal_id

    # UPDATE
    update_data = {
        "vehiculo_id": vehiculo_id,
        "sucursal_id": sucursal_id,
        "fecha_ingreso": str(date.today())
    }
    resp = client.put(f"/vehiculos-sucursal/{id_relacion}", json=update_data, headers=headers)
    assert resp.status_code == 200
    relacion = resp.json()
    assert relacion["vehiculo_id"] == vehiculo_id

    # LIST
    resp = client.get("/vehiculos-sucursal/", headers=headers)
    assert resp.status_code == 200
    relaciones = resp.json()
    assert any(r["id_relacion"] == id_relacion for r in relaciones)

    # DELETE
    resp = client.delete(f"/vehiculos-sucursal/{id_relacion}", headers=headers)
    assert resp.status_code == 200

    # VERIFY DELETE
    resp = client.get(f"/vehiculos-sucursal/{id_relacion}", headers=headers)
    assert resp.status_code == 404