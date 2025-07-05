from pydantic import BaseModel

class MensajeOut(BaseModel):
    mensaje: str