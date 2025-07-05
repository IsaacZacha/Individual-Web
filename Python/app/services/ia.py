from google import genai
from google.genai import types
import os

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
client = genai.Client(api_key=GEMINI_API_KEY)

async def generar_resumen(prompt: str, max_tokens: int = 120) -> str:
    config = types.GenerateContentConfig(
        max_output_tokens=max_tokens,  # ignore type 
        temperature=0.1 # ignore type
    )
    response = client.models.generate_content(
        model="models/gemini-1.5-flash",  # Cambia por el modelo que prefieras de tu lista
        contents=[prompt],
        config=config
    )
    return response.text if getattr(response, "text", None) else "No se pudo generar el resumen." # type: ignore