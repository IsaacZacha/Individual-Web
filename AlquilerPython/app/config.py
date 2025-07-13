from pydantic_settings import BaseSettings
from typing import Optional
import os
from dotenv import load_dotenv

load_dotenv()

class Settings(BaseSettings):
    # Database - Configuración directa de Supabase con connection string correcto
    database_url: str = "postgresql://postgres:Isaac2398633.@db.ccfctmavbafpkuitfpjw.supabase.co:5432/postgres"
    
    # Supabase
    supabase_url: str = "https://ccfctmavbafpkuitfpjw.supabase.co"
    supabase_anon_key: str = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImNjZmN0bWF2YmFmcGt1aXRmcGp3Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwNDkwODUsImV4cCI6MjA2ODYyNTA4NX0.0hDLEAvg1amxB0szGKuutJvyl4liv-du8w0WvJNDtEI"
    supabase_service_key: str = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImNjZmN0bWF2YmFmcGt1aXRmcGp3Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1MzA0OTA4NSwiZXhwIjoyMDY4NjI1MDg1fQ.dG_GczoZ5vNTefG2Oh7jR9M1J9070vdHHTNAYd1-CDs"
    
    # Application
    app_name: str = "Sistema de Alquiler de Vehículos"
    app_version: str = "1.0.0"
    debug: bool = True
    
    # Security
    secret_key: str = os.getenv("SECRET_KEY", "default_secret_key")
    
    # WebSocket
    ws_host: str = os.getenv("WS_HOST", "localhost")
    ws_port: int = int(os.getenv("WS_PORT", "8000"))
    
    class Config:
        env_file = ".env"

settings = Settings()
