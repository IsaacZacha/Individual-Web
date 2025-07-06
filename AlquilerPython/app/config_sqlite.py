from pydantic_settings import BaseSettings
from typing import Optional
import os
from dotenv import load_dotenv

load_dotenv()

class Settings(BaseSettings):
    # Database (SQLite para desarrollo local)
    database_url: str = "sqlite:///./alquiler.db"
    
    # Supabase (para producción)
    supabase_url: str = os.getenv("SUPABASE_URL", "")
    supabase_anon_key: str = os.getenv("SUPABASE_ANON_KEY", "")
    supabase_service_key: str = os.getenv("SUPABASE_SERVICE_KEY", "")
    
    # Application
    app_name: str = os.getenv("APP_NAME", "Sistema de Alquiler")
    app_version: str = os.getenv("APP_VERSION", "1.0.0")
    debug: bool = os.getenv("DEBUG", "True").lower() == "true"
    
    # Security
    secret_key: str = os.getenv("SECRET_KEY", "default_secret_key")
    
    # WebSocket
    ws_host: str = os.getenv("WS_HOST", "localhost")
    ws_port: int = int(os.getenv("WS_PORT", "8000"))
    
    class Config:
        env_file = ".env"

settings = Settings()
