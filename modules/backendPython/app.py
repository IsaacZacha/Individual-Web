from flask import Flask, request, jsonify
from ariadne import graphql_sync
from ariadne.explorer import ExplorerGraphiQL
from models import db
from config import Config
from schemas.schema import schema
from flask_cors import CORS

# Configuración de Flask
app = Flask(__name__)
app.config.from_object(Config)
CORS(app, resources={r"/*": {"origins": "*"}})  # CORS explícito para todas las rutas
db.init_app(app)


graphiql = ExplorerGraphiQL()

# GraphQL
@app.route("/graphql", methods=["GET"])
def graphql_playground():
    return graphiql.html("_"), 200

@app.route("/graphql", methods=["POST"])
def graphql_server():
    data = request.get_json()
    success, result = graphql_sync(schema, data, context_value=request, debug=True)
    return jsonify(result), 200 if success else 400


if __name__ == "__main__":
    with app.app_context():
        db.create_all()
    # Importante: use_reloader=False evita conflictos con WebSockets
    app.run(host="127.0.0.1", port=2000, debug=True, use_reloader=False)