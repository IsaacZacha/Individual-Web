from google import genai
import os

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
client = genai.Client(api_key=GEMINI_API_KEY)

for model in client.models.list():
    print(model.name)