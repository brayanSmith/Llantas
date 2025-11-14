<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //vamos a crear unos 5 proveedores de ejemplo
        \App\Models\Proveedor::factory()->count(5)->create();
    }
}
