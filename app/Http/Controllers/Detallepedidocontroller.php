<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetallePedidoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(DetallePedido::with(['pedido', 'producto'])->get(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_gramos' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($validated['producto_id']);

        if ($producto->stock_gramos < $validated['cantidad_gramos']) {
            return response()->json(['message' => 'No hay suficiente stock disponible'], 400);
        }

        $subtotal = ($validated['cantidad_gramos'] / 1000) * $producto->precio_por_kilo;

        $detalle = DetallePedido::create([
            'pedido_id' => $validated['pedido_id'],
            'producto_id' => $validated['producto_id'],
            'cantidad_gramos' => $validated['cantidad_gramos'],
            'subtotal' => $subtotal,
        ]);

        $producto->decrement('stock_gramos', $validated['cantidad_gramos']);

        $pedido = Pedido::find($validated['pedido_id']);
        $pedido->increment('total', $subtotal);

        return response()->json($detalle->load(['pedido', 'producto']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $detalle = DetallePedido::with(['pedido', 'producto'])->find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle no encontrado'], 404);
        }

        return response()->json($detalle, 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $detalle = DetallePedido::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle no encontrado'], 404);
        }

        $validated = $request->validate([
            'cantidad_gramos' => 'required|integer|min:1',
        ]);

        $producto = $detalle->producto;
        $diferencia = $validated['cantidad_gramos'] - $detalle->cantidad_gramos;

        if ($diferencia > 0 && $producto->stock_gramos < $diferencia) {
            return response()->json(['message' => 'No hay suficiente stock disponible'], 400);
        }

        $nuevoSubtotal = ($validated['cantidad_gramos'] / 1000) * $producto->precio_por_kilo;
        $diferenciaSubtotal = $nuevoSubtotal - $detalle->subtotal;

        $detalle->update([
            'cantidad_gramos' => $validated['cantidad_gramos'],
            'subtotal' => $nuevoSubtotal,
        ]);

        $producto->decrement('stock_gramos', $diferencia);
        $detalle->pedido->increment('total', $diferenciaSubtotal);

        return response()->json($detalle->load(['pedido', 'producto']), 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $detalle = DetallePedido::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle no encontrado'], 404);
        }

        $detalle->producto->increment('stock_gramos', $detalle->cantidad_gramos);
        $detalle->pedido->decrement('total', $detalle->subtotal);
        $detalle->delete();

        return response()->json(['message' => 'Detalle eliminado'], 200);
    }
}