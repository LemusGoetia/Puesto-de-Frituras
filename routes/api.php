<?php

use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('productos', ProductoController::class);
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('detalle-pedidos', DetallePedidoController::class);