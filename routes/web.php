<?php

use Dompdf\Adapter\PDFLib;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PedidoPDFPendienteController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/pos', \App\Livewire\POS::class)->name('pos');

    // Ruta para el índice de pedidos
    Route::get('/pedidos', function () {
        return redirect('/admin/pedidos');
    })->name('pedidos.index');

    Route::get('/pedido/{id}/pdf', [PedidoPDFPendienteController::class, 'stream'])->name('pedidos.pdf.stream');
    Route::get('/pedido/{id}/pdf/download', [PedidoPDFPendienteController::class, 'download'])->name('pedidos.pdf.download');

    Route::get('/pedidoFacturado/{id}/pdf', [\App\Http\Controllers\PedidoPDFacturadoController::class, 'stream'])->name('pedidosFacturados.pdf.stream');
    Route::get('/pedidoFacturado/{id}/pdf/download', [\App\Http\Controllers\PedidoPDFacturadoController::class, 'download'])->name('pedidosFacturados.pdf.download');

    Route::get('/produccion/{id}/pdf', [\App\Http\Controllers\ProduccionPDFController::class, 'stream'])->name('producciones.pdf.stream');
    Route::get('/produccion/{id}/pdf/download', [\App\Http\Controllers\ProduccionPDFController::class, 'download'])->name('producciones.pdf.download');

    Route::get('/compras/{id}/pdf', [\App\Http\Controllers\CompraPdfController::class, 'stream'])->name('compras-pdf.stream');
    Route::get('/compras/{id}/pdf/download', [\App\Http\Controllers\CompraPdfController::class, 'download'])->name('compras-pdf.download');
});




require __DIR__.'/auth.php';
