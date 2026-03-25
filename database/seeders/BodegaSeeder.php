<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BodegaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bodegas')->insert([
            [
                'nombre_bodega' => 'Outlet',
                'ubicacion_bodega' => 'NA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_bodega' => 'Economi',
                'ubicacion_bodega' => 'NA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
