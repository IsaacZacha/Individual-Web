from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

# Crear la aplicación FastAPI
app = FastAPI(
    title="Sistema de Alquiler de Vehículos",
    description="API para gestión de alquiler de vehículos",
    version="1.0.0"
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
def read_root():
    return {
        "message": "Bienvenido al Sistema de Alquiler de Vehículos",
        "version": "1.0.0",
        "docs": "/docs",
        "status": "FastAPI funcionando correctamente"
    }

@app.get("/health")
def health_check():
    return {"status": "healthy", "framework": "FastAPI"}

@app.get("/test")
def test_endpoint():
    return {"message": "Endpoint de prueba funcionando", "framework": "FastAPI"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
