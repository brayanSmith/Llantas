<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_gasto')->unique();
            $table->string('concepto_gasto');
            $table->text('descripcion_gasto')->nullable();
            $table->decimal('monto_gasto', 15, 2);
            $table->date('fecha_gasto');
            $table->string('cuenta_gasto');
            $table->string('subcuenta_gasto')->nullable();
            $table->string('comprobante_gasto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
