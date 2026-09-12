<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Pedido::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'sometimes|string|max:255',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $pedido = Pedido::create($validated);

        return response()->json($pedido, 201);
    }

    public function show(string $id): JsonResponse
    {
        $pedido = Pedido::with('detalles.producto')->find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        return response()->json($pedido, 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        $validated = $request->validate([
            'fecha' => 'sometimes|required|date',
            'estado' => 'sometimes|required|string|max:255',
            'total' => 'sometimes|required|numeric|min:0',
        ]);

        $pedido->update($validated);

        return response()->json($pedido, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        $pedido->detalles()->delete();
        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado'], 200);
    }
}