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
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('marca')->unique();
            $table->string('descripcion_marca')->nullable();
            $table->timestamps();
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_categoria')->unique();
            $table->boolean('aplica_inventario')->default(true);
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->enum('categoria', ['LLANTA', 'RIN', 'SERVICIO', 'OTRO'])->nullable();
            $table->enum('tipo', ['NUEVO', 'USADO'])->nullable();
            $table->boolean('inventariable')->default(true);
            //Llantas
            $table->string('ancho')->nullable();
            $table->string('perfil')->nullable();
            $table->string('construccion')->nullable();
            $table->string('rin')->nullable();
            //Rines
            $table->string('diametro')->nullable();

            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            $table->string('referencia_producto')->nullable();
            $table->string('descripcion_producto')->nullable();
            $table->decimal('costo_producto', 10, 2)->default(0);//Ira cambiando de acuerdo a las entradas
            $table->decimal('valor_detal', 10, 2)->default(0);
            $table->decimal('valor_mayorista', 10, 2)->default(0);
            $table->decimal('valor_sin_instalacion', 10, 2)->default(0);
            $table->string('imagen_producto')->nullable();
            $table->string('concatenar_codigo_nombre')->nullable();
            $table->timestamps();
        });

        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['TEXTO', 'NUMERO', 'DECIMAL', 'ENUM', 'SEPARADOR']); // Replace with actual enum values
            $table->json('opciones')->nullable(); // Only used if tipo is ENUM
            $table->text('valor_por_defecto')->nullable();
            $table->timestamps();
        });

        Schema::create('atributo_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atributo_id')->constrained('atributos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('valor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributo_productos');
        Schema::dropIfExists('atributos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('marcas');
    }
};
