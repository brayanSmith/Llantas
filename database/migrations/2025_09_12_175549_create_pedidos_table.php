<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            //$table->string('codigo')->unique()->nullable();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->enum('estado', ['PENDIENTE', 'COMPLETADO'])->default(value: 'PENDIENTE');
            $table->enum('estado_pago', ['EN_CARTERA', 'SALDADO', 'NO_APLICA'])->default(value: 'EN_CARTERA');
            //$table->enum('tipo_pedido', ['VENTA', 'COTIZACION'])->default(value: 'VENTA');
            $table->enum('tipo_pago', ['CONTADO', 'APARTADO', 'CONTRA_ENTREGA'])->default(value: 'CONTADO');
            $table->enum('tipo_precio', ['DETAL', 'MAYORISTA', 'OTRO'])->default(value: 'DETAL');
            $table->foreignId('id_puc')->constrained('pucs')->nullable();//Medio de Pago
            $table->foreignId('bodega_id')->constrained('bodegas')->nullable()->default(1);
            $table->text('observacion')->nullable();
            $table->text('observacion_pago')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0)->nullable();
            $table->decimal('flete', 12, 2)->default(0)->nullable();
            $table->decimal('total_a_pagar', 12, 2)->default(0);
            $table->decimal('abono', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->boolean('aplica_turno')->default(false);
            $table->string('turno')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
