<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medida;

class MedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Medida::create([
            'nombre_medida' => 'Unidad',
            'tipo_medida' => 'CANTIDAD',
            'descripcion_medida' => 'Unidad de medida estándar',
        ]);
        Medida::create([
            'nombre_medida' => 'Kilogramo',
            'tipo_medida' => 'PESO',
            'descripcion_medida' => 'Unidad de medida de peso',
        ]);
        Medida::create([
            'nombre_medida' => 'Litro',
            'tipo_medida' => 'VOLUMEN',
            'descripcion_medida' => 'Unidad de medida de volumen',
        ]);
        Medida::create([
            'nombre_medida' => 'Metro',
            'tipo_medida' => 'LONGITUD',
            'descripcion_medida' => 'Unidad de medida de longitud',
        ]);
        Medida::create([
            'nombre_medida' => 'Caja',
            'tipo_medida' => 'CANTIDAD',
            'descripcion_medida' => 'Unidad de medida en cajas',
        ]);
    }
}
