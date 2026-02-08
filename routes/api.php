<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockSalidasController;

// Ruta para consultar el stock de un producto
//Route::get('productos/{id}/stock', [StockSalidasController::class, 'showStock']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/recalcular-stock', [StockSalidasController::class, 'recalcularStockArray']);
