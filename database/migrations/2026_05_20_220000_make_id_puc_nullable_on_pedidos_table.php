<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedidos') || !Schema::hasColumn('pedidos', 'id_puc')) {
            return;
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('id_puc')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pedidos') || !Schema::hasColumn('pedidos', 'id_puc')) {
            return;
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('id_puc')->nullable(false)->change();
        });
    }
};
