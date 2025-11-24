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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('numero_documento')->unique();
            $table->string('razon_social');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('representante_legal')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('novedad')->nullable();
            $table->foreignId('ruta_id')->nullable()->constrained('rutas')->cascadeOnDelete();
            $table->foreignId('comercial_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('tipo_cliente', ['ELECTRONICO', 'REMISIONADO'])->default('ELECTRONICO');
            $table->string('rut_imagen')->nullable();
            $table->enum('retenedor_fuente', ['SI', 'NO'])->default('NO');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
