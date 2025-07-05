<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function preguntar(Request $request)
    {
        $apiKey = env('GEMINI_API_KEY');
        $entidad = $request->input('entidad');
        $pregunta = $request->input('pregunta'); 

        // Obtén los datos de la entidad
        $modelo = "\\App\\Models\\$entidad";
        if (!class_exists($modelo)) {
            return response()->json(['message' => 'Entidad no válida'], 400);
        }
        $datos = $modelo::all()->toArray();

        // Prepara el prompt para Gemini
        $prompt = "Datos de $entidad: " . json_encode($datos) . ". Pregunta: " . $pregunta;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey",
            [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ]
        );

        return response()->json($response->json());
    }
}