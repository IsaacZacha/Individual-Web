import os

class Config:
    SQLALCHEMY_DATABASE_URI = os.environ.get('DATABASE_URL') or \
            'postgresql+psycopg2://postgres:123123@localhost:5432/proceso_alquiler'
    SQLALCHEMY_TRACK_MODIFICATIONS = False
