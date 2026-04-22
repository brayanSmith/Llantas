<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Empresa::create([
            'nombre_empresa' => 'Servillantas S.A.S',
            'nit_empresa' => '900123456-7',
            'direccion_empresa' => 'Calle 123 #45-67, Bogotá',
            'telefono_empresa' => '3101234567',
            'email_empresa' => 'info@servillantas.com',
        ]);
    }
}
