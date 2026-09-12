<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Producto::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'precio_por_kilo' => 'required|numeric|min:0',
            'stock_gramos' => 'required|integer|min:0',
        ]);

        $producto = Producto::create($validated);

        return response()->json($producto, 201);
    }

    public function show(string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|max:255',
            'precio_por_kilo' => 'sometimes|required|numeric|min:0',
            'stock_gramos' => 'sometimes|required|integer|min:0',
        ]);

        $producto->update($validated);

        return response()->json($producto, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        if ($producto->detalles()->exists()) {
            return response()->json(['message' => 'No se puede eliminar, el producto tiene pedidos relacionados'], 400);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado'], 200);
    }
}